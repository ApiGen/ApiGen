<?php declare(strict_types=1);

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeConfigPathResolver;
use Mockery;
use PHPUnit\Framework\TestCase;

class ThemeConfigPathResolverTest extends TestCase
{

    public function testResolve(): void
    {
        $themeConfigPathResolver = new ThemeConfigPathResolver(__DIR__ . '/ThemeConfigPathResolverSource');

        $configPath = $themeConfigPathResolver->resolve('/config.neon');
        $this->assertSame(__DIR__  . '/ThemeConfigPathResolverSource/config.neon', $configPath);

        $configPath = $themeConfigPathResolver->resolve('config.neon');
        $this->assertSame(__DIR__  . '/ThemeConfigPathResolverSource/config.neon', $configPath);
    }
}
