<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Parameter;

use ApiGen\ModularConfiguration\Contract\Parameter\ParameterProviderInterface;
use Nette\DI\Container;
use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class ParameterProvider implements ParameterProviderInterface
{
    /**
     * @var mixed[]
     */
    private $parameters = [];


    public function __construct(SymfonyContainer $symfonyContainer, ?Container $netteContainer = null)
    {
        if ($symfonyContainer !== null) {
            $containerParameters = $symfonyContainer->getParameterBag()->all();
            $this->parameters = $this->unsetSymfonyDefaultParameters($containerParameters);
        } elseif ($netteContainer !== null) {
            $containerParameters = $netteContainer->getParameters();
            $this->parameters = $this->unsetNetteDefaultParameters($containerParameters);
        }
    }

    /**
     * @return mixed[]
     */
    public function provide(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed[] $containerParameters
     * @return mixed[]
     */
    private function unsetNetteDefaultParameters(array $containerParameters): array
    {
        unset(
            $containerParameters['appDir'], $containerParameters['wwwDir'],
            $containerParameters['debugMode'], $containerParameters['productionMode'],
            $containerParameters['consoleMode'], $containerParameters['tempDir']
        );

        return $containerParameters;
    }

    /**
     * @param mixed[] $containerParameters
     * @return mixed[]
     */
    private function unsetSymfonyDefaultParameters(array $containerParameters): array
    {
        foreach ($containerParameters as $name => $value) {
            if (Strings::startsWith($name, 'kernel')) {
                unset ($containerParameters[$name]);
            }
        }
        return $containerParameters;
    }
}
