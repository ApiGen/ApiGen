<?php declare(strict_types=1);

namespace ApiGen\StringRouting;

use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class StringRouter
{
    /**
     * @var string
     */
    private const FALLBACK_PATH = '/';

    /**
     * @var RouteInterface[]
     */
    private $routes = [];

    public function addRoute(RouteInterface $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * @param mixed $argument
     */
    public function buildRoute(string $name, $argument): string
    {
        foreach ($this->routes as $route) {
            if ($route->match($name)) {
                return $route->constructUrl($argument);
            }
        }

        return self::FALLBACK_PATH;
    }
}
