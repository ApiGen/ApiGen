<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Contract\Route;

interface RouteInterface
{
    public function match(string $name): bool;

    /**
     * @param mixed $argument
     */
    public function constructUrl($argument): string;
}
