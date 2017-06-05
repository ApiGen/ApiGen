<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Event\CreateTemplateEvent;
use ApiGen\Event\GenerateProgressEvent;
use ApiGen\Exception\Templating\FailedRenderFileException;
use ApiGen\Templating\Parameters\ParameterBag;
use ApiGen\Utils\FileSystem;
use Latte\Engine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

final class TemplateRenderer
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Engine
     */
    private $latteEngine;

    public function __construct(Engine $latteEngine, EventDispatcherInterface $eventDispatcher)
    {
        $this->latteEngine = $latteEngine;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderToFile(string $templateFile, string $destinationFile, array $parameters = []): void
    {
        $parametersBag = new ParameterBag;

        $createTemplateEvent = new CreateTemplateEvent($parametersBag);
        $this->eventDispatcher->dispatch(CreateTemplateEvent::class, $createTemplateEvent);

        $parametersBag->addParameters($parameters);

        FileSystem::ensureDirectoryExistsForFile($destinationFile);

        try {
            file_put_contents(
                $destinationFile,
                $this->latteEngine->renderToString($templateFile, $parametersBag->getParameters())
            );
        } catch (Throwable $throwable) {
            throw new FailedRenderFileException(sprintf(
                'Rendering of "%s" template to "%s" file failed due to: %s',
                $templateFile,
                $destinationFile,
                $throwable->getMessage()
            ));
        }

        $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
    }
}
