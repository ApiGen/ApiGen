<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementsInterface;

class Elements implements ElementsInterface
{

    /**
     * {@inheritdoc}
     */
    public function getClassTypeList()
    {
        return [self::CLASSES, self::EXCEPTIONS, self::INTERFACES, self::TRAITS];
    }


    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return [self::CLASSES, self::CONSTANTS, self::EXCEPTIONS, self::FUNCTIONS, self::INTERFACES, self::TRAITS];
    }


    /**
     * {@inheritdoc}
     */
    public function getEmptyList()
    {
        $emptyList = [];
        foreach ($this->getAll() as $type) {
            $emptyList[$type] = [];
        }
        return $emptyList;
    }
}
