<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Generator\Event\GenerateProgressEvent;
use Latte\Engine;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
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
        // @todo: create template event here

//        $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
//
//        $createTemplateEvent = new CreateTemplateEvent($parametersBag);
//        $this->eventDispatcher->dispatch(CreateTemplateEvent::class, $createTemplateEvent);

        $template = $this->templateFactory->create();
        $template->save($templateFile, $destinationFile, $parameters);

//        /**
//         * @param string $fileDestination
//         * @param mixed[] $parameters
//         */
//        public function save(string $templateFile, string $fileDestination, array $parameters = []): void
//    {
//        FileSystem::ensureDirectoryExists($fileDestination);
//
//        $parameters = array_merge($this->parameters, $parameters);
//
//        file_put_contents($fileDestination, $this->latteEngine->renderToString($templateFile, $parameters));
//    }


        $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
    }
}
