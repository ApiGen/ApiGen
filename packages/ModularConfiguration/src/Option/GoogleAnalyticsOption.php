<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Option;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

final class GoogleAnalyticsOption implements OptionInterface
{
    /**
     * @var string
     */
    public const NAME = 'googleAnalytics';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param mixed $value
     */
    public function resolveValue($value): string
    {
        return $value;
    }
}
