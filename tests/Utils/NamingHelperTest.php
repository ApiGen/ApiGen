<?php declare(strict_types=1);

namespace ApiGen\Tests\Utils;

use ApiGen\Utils\NamingHelper;
use PHPUnit\Framework\TestCase;

final class NamingHelperTest extends TestCase
{
    public function test(): void
    {
        $this->assertSame('ApiGen.Utils', NamingHelper::nameToFilePath('ApiGen\Utils'));
    }
}
