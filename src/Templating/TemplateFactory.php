<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Templating\Exceptions\UnsupportedElementException;
use Latte;
use Nette\Utils\ArrayHash;

final class TemplateFactory implements TemplateFactoryInterface
{

    const ELEMENT_SOURCE = 'source';
    const ELEMENT_NAMESPACE = 'namespace';
    const ELEMENT_ANNOTATION_GROUP = 'annotationGroup';

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
        return $this->buildTemplate();
    }


    public function createForType(string $type): Template
    {
        $template = $this->buildTemplate();
        $template->setFile($this->templateNavigator->getTemplatePath($type));
        $template->setSavePath($this->templateNavigator->getTemplateFileName($type));
        $template = $this->setEmptyDefaults($template);
        return $template;
    }


    /**
     * @param string $name
     * @param ReflectionElement|string $element
     * @throws \Exception
     * @return Template
     */
    public function createNamedForElement(string $name, $element): Template
    {
        $template = $this->buildTemplate();
        $template->setFile($this->templateNavigator->getTemplatePath($name));

        if ($name === self::ELEMENT_SOURCE) {
            $template->setSavePath($this->templateNavigator->getTemplatePathForSourceElement($element));
        } elseif ($name === self::ELEMENT_NAMESPACE) {
            $template->setSavePath($this->templateNavigator->getTemplatePathForNamespace($element));
        } elseif ($name === self::ELEMENT_ANNOTATION_GROUP) {
            $template->setSavePath($this->templateNavigator->getTemplatePathForAnnotationGroup($element));
        } else {
            throw new UnsupportedElementException($name . ' is not supported template type.');
        }
        return $template;
    }


    public function createForReflection(ElementReflectionInterface $element): Template
    {
        $template = $this->buildTemplate();

        if ($element instanceof ClassReflectionInterface) {
            $template->setFile($this->templateNavigator->getTemplatePath('class'));
            $template->setSavePath($this->templateNavigator->getTemplatePathForClass($element));
        } elseif ($element instanceof ConstantReflectionInterface) {
            $template->setFile($this->templateNavigator->getTemplatePath('constant'));
            $template->setSavePath($this->templateNavigator->getTemplatePathForConstant($element));
        } elseif ($element instanceof FunctionReflectionInterface) {
            $template->setFile($this->templateNavigator->getTemplatePath('function'));
            $template->setSavePath($this->templateNavigator->getTemplatePathForFunction($element));
        }

        return $template;
    }


    private function buildTemplate(): Template
    {
        if ($this->builtTemplate === null) {
            $options = $this->configuration->getOptions();
            $template = new Template($this->latteEngine);
            $template->setParameters([
                'config' => ArrayHash::from($options),
                'basePath' => $options[CO::TEMPLATE][TCO::TEMPLATES_PATH]
            ]);
            $this->builtTemplate = $template;
        }
        return $this->templateElementsLoader->addElementsToTemplate($this->builtTemplate);
    }


    private function setEmptyDefaults(Template $template): Template
    {
        return $template->setParameters([
            'namespace' => null,
            'package' => null
        ]);
    }
}
