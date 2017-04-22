<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Parser\ParserSource;

/**
 * Huge and small
 *
 * description.
 *
 * @author Me.
 */
class SomeClass
{
    /**
     * @var int
     */
    public $someProperty;

    public function SomeMethod()/*: void - brokes old parser */
    {

    }
}
