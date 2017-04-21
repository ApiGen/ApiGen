<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Event\CreateTemplateEvent;
use Latte\Engine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TemplateFactory implements TemplateFactoryInterface
{
    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Engine $latteEngine, EventDispatcherInterface $eventDispatcher)
    {
        $this->latteEngine = $latteEngine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(): Template
    {
        $template = new Template($this->latteEngine, $this->eventDispatcher);

        $createTemplateEvent = new CreateTemplateEvent($template);
        $this->eventDispatcher->dispatch(CreateTemplateEvent::class, $createTemplateEvent);

        return $template;
    }
}
