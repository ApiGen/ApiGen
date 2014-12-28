<?php

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Templating\Filters\Helpers\Strings;
use PHPUnit_Framework_TestCase;


class StringsTest extends PHPUnit_Framework_TestCase
{

	public function testSplit()
	{
		$this->assertSame(['@license', 'MIT'], Strings::split('@license MIT'));
		$this->assertSame(['@author', 'Some author'], Strings::split('@author Some author'));
	}

}
