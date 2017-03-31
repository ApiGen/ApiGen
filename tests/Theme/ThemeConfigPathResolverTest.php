<?php

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeConfigPathResolver;
use ApiGen\Utils\FileSystem;
use PHPUnit_Framework_TestCase;

class ThemeConfigPathResolverTest extends PHPUnit_Framework_TestCase
{

    public function testResolve()
    {
        $themeConfigPathResolver = new ThemeConfigPathResolver(__DIR__ . '/ThemeConfigPathResolverSource');

        $configPath = $themeConfigPathResolver->resolve('/config.neon');
        $this->assertSame(FileSystem::getAbsolutePath(__DIR__  . '/ThemeConfigPathResolverSource/config.neon'), $configPath);

        $configPath = $themeConfigPathResolver->resolve('config.neon');
        $this->assertSame(FileSystem::getAbsolutePath(__DIR__  . '/ThemeConfigPathResolverSource/config.neon'), $configPath);
    }
}
