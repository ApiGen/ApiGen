<?php declare(strict_types=1);

namespace ApiGen\Parser;

final class ElementEmptyListFactory
{
    /**
     * @return mixed[]
     */
    public static function createBasicEmptyList(): array
    {
        return [
            'classes' => [],
            'interfaces' => [],
            'traits' => [],
            'functions' => [],
        ];
    }
}
