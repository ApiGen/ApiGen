<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Configuration\Theme\ThemeConfigOptions;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Exceptions\UnsupportedElementException;
use Latte;

/**
 * @todo decouple to collector
 */
final class TemplateFactory implements TemplateFactoryInterface
{
    /**
     * @var string
     */
    public const ELEMENT_SOURCE = 'source';

    /**
     * @var string
     */
    public const ELEMENT_NAMESPACE = 'namespace';

    /**
     * @var string
     */
    public const ELEMENT_ANNOTATION_GROUP = 'annotationGroup';

    /**
     * @var Latte\Engine
     */
    private $latteEngine;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateNavigator
     */
    private $templateNavigator;

    /**
     * @var TemplateElementsLoader
     */
    private $templateElementsLoader;

    /**
     * @var Template
     */
    private $builtTemplate;

    public function __construct(
        Latte\Engine $latteEngine,
        Configuration $configuration,
        TemplateNavigator $templateNavigator,
        TemplateElementsLoader $templateElementsLoader
    ) {
        $this->latteEngine = $latteEngine;
        $this->configuration = $configuration;
        $this->templateNavigator = $templateNavigator;
        $this->templateElementsLoader = $templateElementsLoader;
    }

    public function create(): Template
    {
        if ($this->builtTemplate === null) {
            $options = $this->configuration->getOptions();
            $template = new Template($this->latteEngine);
            $template->setParameters([
                'title' => $this->configuration->getTitle(),
                'googleAnalytics' => $this->configuration->getGoogleAnalytics(),
                'basePath' => $options[ConfigurationOptions::TEMPLATE][ThemeConfigOptions::TEMPLATES_PATH]
            ]);
            $this->builtTemplate = $template;
        }

        $this->setEmptyDefaults($this->builtTemplate);

        $this->templateElementsLoader->addElementsToTemplate($this->builtTemplate);

        return $this->builtTemplate;
    }

    public function createForType(string $type): Template
    {
        $template = $this->create();
        $template->setFile($this->templateNavigator->getTemplatePath($type));
        $template->setSavePath($this->templateNavigator->getTemplateFileName($type));
        return $template;
    }

    /**
     * @param string $name
     * @param ElementReflectionInterface|string $element
     * @throws \Exception
     * @return Template
     */
    public function createNamedForElement(string $name, $element): Template
    {
        $template = $this->create();

        $templateFile = $this->templateNavigator->getTemplatePath($name);
        $template->setFile($templateFile);

        if ($name === self::ELEMENT_SOURCE) {
            $template->setSavePath($this->templateNavigator->getTemplatePathForSourceElement($element));
        } elseif ($name === self::ELEMENT_NAMESPACE) {
            $template->setSavePath($this->templateNavigator->getTemplatePathForNamespace($element));
        } elseif ($name === self::ELEMENT_ANNOTATION_GROUP) {
            $template->setSavePath($this->templateNavigator->getTemplatePathForAnnotationGroup($element));
        } else {
            throw new UnsupportedElementException(sprintf(
                '"%s" is not supported template type.',
                $name
            ));
        }

        return $template;
    }

    public function createForReflection(ElementReflectionInterface $element): Template
    {
        $template = $this->create();

        if ($element instanceof ClassReflectionInterface) {
            $template->setFile($this->templateNavigator->getTemplatePath('class'));
            $template->setSavePath($this->templateNavigator->getTemplatePathForClass($element));
        } elseif ($element instanceof FunctionReflectionInterface) {
            $template->setFile($this->templateNavigator->getTemplatePath('function'));
            $template->setSavePath($this->templateNavigator->getTemplatePathForFunction($element));
        }

        return $template;
    }

    private function setEmptyDefaults(Template $template): void
    {
        $template->setParameters([
            'namespace' => null,
        ]);
    }
}
