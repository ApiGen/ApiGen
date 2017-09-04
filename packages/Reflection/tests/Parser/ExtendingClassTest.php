<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ExtendingClassTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/ExtendingSources']);
        $this->parser->parse();

        $this->assertTrue(class_exists(ExtendingClass::class));
    }
}
