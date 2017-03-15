<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface InNamespaceInterface
{

    /**
     * @deprecated To be removed with ApiGen\ElementParser
     * @return string
     */
    public function getDeclaringClassName();


    /**
     * @return string[]
     */
    public function getNamespaceAliases();
}
