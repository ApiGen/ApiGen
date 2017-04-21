<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Templating\Parameters\ParameterBag;
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
        $parametersBag = new ParameterBag;

        $createTemplateEvent = new CreateTemplateEvent($parametersBag);
        $this->eventDispatcher->dispatch(CreateTemplateEvent::class, $createTemplateEvent);

        $template = new Template($this->latteEngine, $parametersBag);

        return $template;
    }
}
