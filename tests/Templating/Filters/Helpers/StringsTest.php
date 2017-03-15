<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Templating\Filters\Helpers\Strings;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{

    public function testSplit()
    {
        $this->assertSame(['@license', 'MIT'], Strings::split('@license MIT'));
        $this->assertSame(['@author', 'Some author'], Strings::split('@author Some author'));
    }
}
