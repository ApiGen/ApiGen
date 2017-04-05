<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;

final class Elements implements ElementsInterface
{
    /**
     * @return string[]
     */
    public function getClassTypeList(): array
    {
        return [self::CLASSES, self::EXCEPTIONS, self::INTERFACES, self::TRAITS];
    }

    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return [self::CLASSES, self::EXCEPTIONS, self::FUNCTIONS, self::INTERFACES, self::TRAITS];
    }

    /**
     * @return mixed[]
     */
    public function getEmptyList(): array
    {
        $emptyList = [];
        foreach ($this->getAll() as $type) {
            $emptyList[$type] = [];
        }

        return $emptyList;
    }
}
