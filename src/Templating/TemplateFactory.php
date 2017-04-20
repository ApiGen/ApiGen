<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use Latte;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @todo decouple to collector
 */
final class TemplateFactory implements TemplateFactoryInterface
{
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

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Latte\Engine $latteEngine,
        Configuration $configuration,
        TemplateNavigator $templateNavigator,
        TemplateElementsLoader $templateElementsLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->latteEngine = $latteEngine;
        $this->configuration = $configuration;
        $this->templateNavigator = $templateNavigator;
        $this->templateElementsLoader = $templateElementsLoader;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(): Template
    {
        if ($this->builtTemplate === null) {
            $template = new Template($this->latteEngine, $this->eventDispatcher);
            $template->setParameters([
                'title' => $this->configuration->getTitle(),
                'googleAnalytics' => $this->configuration->getGoogleAnalytics(),
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

    public function createForReflection(ElementReflectionInterface $element): Template
    {
        $template = $this->create();

        if ($element instanceof ClassReflectionInterface) {
            if ($element->isTrait()) {
                $template->setFile($this->templateNavigator->getTemplatePath('trait'));
            } elseif ($element->isInterface()) {
                $template->setFile($this->templateNavigator->getTemplatePath('interface'));
            } else {
                $template->setFile($this->templateNavigator->getTemplatePath('class'));
            }

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
