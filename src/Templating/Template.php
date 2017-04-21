<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Templating\Parameters\ParameterBag;
use ApiGen\Utils\FileSystem;
use Latte\Engine;

final class Template
{
    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var ParameterBag
     */
    private $parameters;

    public function __construct(Engine $latteEngine, ParameterBag $parameterBag)
    {
        $this->latteEngine = $latteEngine;
        $this->parameters = $parameterBag;
    }

    /**
     * @param mixed[] $parameters
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters->addParameters($parameters);
    }

    /**
     * @param string $fileDestination
     * @param mixed[] $parameters
     */
    public function save(string $templateFile, string $fileDestination, array $parameters = []): void
    {
        FileSystem::ensureDirectoryExists($fileDestination);

        $this->parameters->addParameters($parameters);

        file_put_contents(
            $fileDestination,
            $this->latteEngine->renderToString($templateFile, $this->parameters->getParameters())
        );
    }
}
