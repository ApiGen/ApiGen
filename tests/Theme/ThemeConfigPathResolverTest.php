<?php

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeConfigPathResolver;
use Mockery;
use PHPUnit\Framework\TestCase;

class ThemeConfigPathResolverTest extends TestCase
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
