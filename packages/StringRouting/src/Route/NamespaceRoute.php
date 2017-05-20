<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\StringRouting\Contract\Route\RouteInterface;
use Nette\Utils\Strings;

final class NamespaceRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'namespace';

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param string $argument
     */
    public function constructUrl($argument): string
    {
        return 'namespace-' . Strings::webalize($argument, null, false) . '.html';
    }
}
