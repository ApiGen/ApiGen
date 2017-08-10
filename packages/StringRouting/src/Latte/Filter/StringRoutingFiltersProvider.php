<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Latte\Filter;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\StringRouting\Route\NamespaceRoute;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\StringRouting\Route\SourceCodeRoute;
use ApiGen\StringRouting\StringRouter;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;

final class StringRoutingFiltersProvider implements FilterProviderInterface
{
    /**
     * @var StringRouter
     */
    private $router;

    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    public function __construct(StringRouter $router, ReflectionStorage $reflectionStorage)
    {
        $this->router = $router;
        $this->reflectionStorage = $reflectionStorage;
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
            },

            // use in .latte: {$className|buildLinkIfReflectionFound}
            'buildLinkIfReflectionFound' => function (string $className) {
                $reflection = $this->reflectionStorage->getClassOrInterface($className);
                if ($reflection) {
                    $link = Html::el('a');
                    $link->setAttribute('href', $this->router->buildRoute(ReflectionRoute::NAME, $reflection));
                    $link->setText($className);

                    return $link;
                }

                return $className;
            },
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
