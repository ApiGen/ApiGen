<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Parser\ParserSource;

interface SomeInterface
{
    /**
     * @var string
     */
    /* protected - breaks old parser */const HELLO = 'hi';
}
