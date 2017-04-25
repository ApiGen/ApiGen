<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Parameter;

use ApiGen\ModularConfiguration\Contract\Parameter\ParameterProviderInterface;
use Nette\DI\Container;

final class ParameterProvider implements ParameterProviderInterface
{
    /**
     * @var mixed[]
     */
    private $parameters = [];

    public function __construct(Container $container)
    {
        $containerParameters = $container->getParameters();
        unset(
            $containerParameters['appDir'], $containerParameters['wwwDir'],
            $containerParameters['debugMode'], $containerParameters['productionMode'],
            $containerParameters['consoleMode'], $containerParameters['tempDir']
        );
        $this->parameters = $containerParameters;
    }

    /**
     * @return mixed[]
     */
    public function provide(): array
    {
        return $this->parameters;
    }
}
