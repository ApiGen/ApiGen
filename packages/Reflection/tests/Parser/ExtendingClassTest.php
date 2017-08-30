<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ExtendingClassTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/ExtendingSources']);

        $this->assertTrue(class_exists(ExtendingClass::class));
    }
}
