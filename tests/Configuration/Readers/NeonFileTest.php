<?php declare(strict_types=1);

namespace ApiGen\Tests\Configuration\Readers;

use Nette\DI\Config\Loader;
use PHPUnit\Framework\TestCase;

final class NeonFileTest extends TestCase
{
    public function testRead(): void
    {
        file_put_contents(TEMP_DIR . '/config.neon', 'var: value');

        $configLoader = new Loader;
        $options = $configLoader->load(TEMP_DIR . '/config.neon');

        $this->assertSame(['var' => 'value'], $options);
    }
}
