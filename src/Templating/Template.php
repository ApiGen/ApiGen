<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Generator\Event\GenerateProgressEvent;
use Latte\Engine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Template
{
    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var string
     */
    private $file;

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Engine $latteEngine, EventDispatcherInterface $eventDispatcher)
    {
        $this->latteEngine = $latteEngine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed[] $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters + $this->parameters;
    }

    /**
     * @param string $fileDestination
     * @param mixed[] $parameters
     */
    public function save(string $fileDestination, array $parameters = []): void
    {
        $this->ensureDirectoryExists($fileDestination);

        $parameters = array_merge($this->parameters, $parameters);
        file_put_contents($fileDestination, $this->latteEngine->renderToString($this->file, $parameters));

        $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
    }

    private function ensureDirectoryExists(string $destination): void
    {
        $directory = dirname($destination);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
