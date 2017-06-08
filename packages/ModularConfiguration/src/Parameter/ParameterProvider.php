<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Parameter;

use ApiGen\ModularConfiguration\Contract\Parameter\ParameterProviderInterface;
use Nette\DI\Container;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class ParameterProvider implements ParameterProviderInterface
{
    /**
     * @var mixed[]|ParameterBagInterface
     */
    private $parameters = [];

    public function __construct(?SymfonyContainer $symfonyContainer = null, ?Container $netteContainer = null)
    {
        if ($symfonyContainer !== null) {
            $this->parameters = $symfonyContainer->getParameterBag();
        } elseif ($netteContainer !== null) {
            $containerParameters = $netteContainer->getParameters();
            $this->parameters = $this->unsedNetteDefaultParameters($containerParameters);
        }
    }

    /**
     * @return mixed[]|ParameterBagInterface
     */
    public function provide(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed[] $containerParameters
     * @return mixed[]
     */
    private function unsedNetteDefaultParameters(array $containerParameters): array
    {
        unset(
            $containerParameters['appDir'], $containerParameters['wwwDir'],
            $containerParameters['debugMode'], $containerParameters['productionMode'],
            $containerParameters['consoleMode'], $containerParameters['tempDir']
        );

        return $containerParameters;
    }
}
