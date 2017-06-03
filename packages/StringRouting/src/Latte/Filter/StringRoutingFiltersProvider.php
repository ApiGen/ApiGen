<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Latte\Filter;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\StringRouting\Route\NamespaceRoute;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\StringRouting\Route\SourceCodeRoute;
use ApiGen\StringRouting\StringRouter;
use Nette\InvalidArgumentException;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class StringRoutingFiltersProvider implements LatteFiltersProviderInterface
{
    /**
     * @var StringRouter
     */
    private $router;

    public function __construct(StringRouter $router)
    {
        $this->router = $router;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: <a href="{$namespace|linkNamespace}">{$namespace}</a>
            'linkNamespace' => function (string $namespace): string {
                return $this->router->buildRoute(NamespaceRoute::NAME, $namespace);
            },

            // use in .latte: <a href="{$refleciton|linkReflection}">{$name}</a>
            'linkReflection' => function ($reflection): string {
                $this->ensureFilterArgumentsIsReflection($reflection, 'linkReflection');
                return $this->router->buildRoute(ReflectionRoute::NAME, $reflection);
            },

            // use in .latte: <a href="{$reflection|linkSource}">{$name}</a>
            'linkSource' => function ($reflection): string {
                $this->ensureFilterArgumentsIsReflection($reflection, 'linkSource');
                return $this->router->buildRoute(SourceCodeRoute::NAME, $reflection);
            }
        ];
    }

    /**
     * @param mixed $reflection
     */
    private function ensureFilterArgumentsIsReflection($reflection, string $filterName): void
    {
        if (! $reflection instanceof AbstractReflectionInterface) {
            throw new InvalidArgumentException(sprintf(
                'Argument for filter "%s" has to be type of "%s". "%s" given.',
                 $filterName,
                AbstractReflectionInterface::class,
                is_object($reflection) ? get_class($reflection) : gettype($reflection)
            ));
        }
    }
}
