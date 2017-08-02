<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Parameter;

use ApiGen\ModularConfiguration\Contract\Parameter\ParameterProviderInterface;
use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ParameterProvider implements ParameterProviderInterface
{
    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @param Container|ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $containerParameters = $container->getParameterBag()->all();
        $this->parameters = $this->unsetSymfonyDefaultParameters($containerParameters);
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
    private function unsetSymfonyDefaultParameters(array $containerParameters): array
    {
        foreach ($containerParameters as $name => $value) {
            if (Strings::startsWith($name, 'kernel')) {
                unset($containerParameters[$name]);
            }
        }

        return $containerParameters;
    }
}
