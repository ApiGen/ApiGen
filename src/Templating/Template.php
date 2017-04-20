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
    private $savePath;

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

    public function setSavePath(string $savePath): void
    {
        $this->savePath = $savePath;
    }

    /**
     * @param null|string $file
     * @param mixed[] $parameters
     */
    public function save(?string $file = null, array $parameters = []): void
    {
        $this->savePath = $file ?: $this->savePath;
        $dir = dirname($this->savePath);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $parameters = array_merge($this->parameters, $parameters);
        $content = $this->latteEngine->renderToString($this->file, $parameters);
        file_put_contents($this->savePath, $content);

        $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
    }
}
