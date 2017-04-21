<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use Latte\Engine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Engine
     */
    private $latteEngine;

    public function __construct(
        Engine $latteEngine,
        TemplateFactoryInterface $templateFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->latteEngine = $latteEngine;
        $this->templateFactory = $templateFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $templateFile
     * @param string $destinationFile
     * @param mixed[] $parameters
     */
    public function renderToFile(string $templateFile, string $destinationFile, array $parameters = []): void
    {
        $template = $this->templateFactory->create();
        $template->save($templateFile, $destinationFile, $parameters);

        $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
    }
}
