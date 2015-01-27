<?php

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeConfigPathResolver;
use Mockery;
use PHPUnit_Framework_TestCase;


class ThemeConfigPathResolverTest extends PHPUnit_Framework_TestCase
{

	public function testResolve()
	{
		$themeConfigPathResolver = new ThemeConfigPathResolver(__DIR__ . '/ThemeConfigPathResolverSource');

		$configPath = $themeConfigPathResolver->resolve('/config.neon');
		$this->assertSame(__DIR__  . '/ThemeConfigPathResolverSource/config.neon', $configPath);

		$configPath = $themeConfigPathResolver->resolve('config.neon');
		$this->assertSame(__DIR__  . '/ThemeConfigPathResolverSource/config.neon', $configPath);
	}

}
