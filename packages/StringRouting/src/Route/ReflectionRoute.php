<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class ReflectionRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'reflection';

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param mixed $argument
     */
    public function constructUrl($argument): string
    {
        dump($argument);
        die;
    }
}
