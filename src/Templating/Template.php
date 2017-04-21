<?php declare(strict_types=1);

namespace ApiGen\Templating;

use Latte\Engine;

final class Template
{
    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var mixed[]
     */
    private $parameters = [];

    public function __construct(Engine $latteEngine)
    {
        $this->latteEngine = $latteEngine;
    }

    /**
     * @param mixed[] $parameters
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters = $parameters + $this->parameters;
    }

    /**
     * @param string $fileDestination
     * @param mixed[] $parameters
     */
    public function save(string $templateFile, string $fileDestination, array $parameters = []): void
    {
        $this->ensureDirectoryExists($fileDestination);

        $parameters = array_merge($this->parameters, $parameters);

        file_put_contents($fileDestination, $this->latteEngine->renderToString($templateFile, $parameters));
    }

    private function ensureDirectoryExists(string $destination): void
    {
        $directory = dirname($destination);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
