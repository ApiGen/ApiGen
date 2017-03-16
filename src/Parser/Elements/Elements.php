<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;

final class Elements implements ElementsInterface
{

    public function getClassTypeList(): array
    {
        return [self::CLASSES, self::EXCEPTIONS, self::INTERFACES, self::TRAITS];
    }


    public function getAll(): array
    {
        return [self::CLASSES, self::CONSTANTS, self::EXCEPTIONS, self::FUNCTIONS, self::INTERFACES, self::TRAITS];
    }


    public function getEmptyList(): array
    {
        $emptyList = [];
        foreach ($this->getAll() as $type) {
            $emptyList[$type] = [];
        }
        return $emptyList;
    }
}
