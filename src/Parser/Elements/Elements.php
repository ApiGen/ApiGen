<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
