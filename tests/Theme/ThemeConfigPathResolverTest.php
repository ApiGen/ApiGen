<?php

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeConfigPathResolver;
use ApiGen\Utils\FileSystem;
use PHPUnit_Framework_TestCase;

class ThemeConfigPathResolverTest extends PHPUnit_Framework_TestCase
{

    public function testResolve()
    {
        $themeConfigPathResolver =
            new ThemeConfigPathResolver(__DIR__ . DIRECTORY_SEPARATOR . 'ThemeConfigPathResolverSource');

        $configPath = $themeConfigPathResolver->resolve(DIRECTORY_SEPARATOR . 'config.neon');
        $this->assertSame(
            __DIR__  . DIRECTORY_SEPARATOR . 'ThemeConfigPathResolverSource' . DIRECTORY_SEPARATOR . 'config.neon',
            $configPath);

        $configPath = $themeConfigPathResolver->resolve('config.neon');
        $this->assertSame(
            __DIR__  . DIRECTORY_SEPARATOR . 'ThemeConfigPathResolverSource' . DIRECTORY_SEPARATOR . 'config.neon',
            $configPath);
    }
}
