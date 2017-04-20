<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use Latte\Engine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TemplateFactory implements TemplateFactoryInterface
{
    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateElementsLoader
     */
    private $templateElementsLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Engine $latteEngine,
        ConfigurationInterface $configuration,
        TemplateElementsLoader $templateElementsLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->latteEngine = $latteEngine;
        $this->configuration = $configuration;
        $this->templateElementsLoader = $templateElementsLoader;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(): Template
    {
        $template = new Template($this->latteEngine, $this->eventDispatcher);
        $template->setParameters([
            'title' => $this->configuration->getTitle(),
            'googleAnalytics' => $this->configuration->getGoogleAnalytics(),
        ]);

        $this->templateElementsLoader->addElementsToTemplate($template);

        return $template;
    }
}
