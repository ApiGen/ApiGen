<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use DifferentVendor\DifferentClass;
use MyVendor\MyClass;

final class UseDifferentVendorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $parser = $this->container->get(Parser::class);

        $parser->parseFilesAndDirectories([__DIR__ . '/DifferentVendorSources/src']);

        $this->assertTrue(class_exists(MyClass::class));
        $this->assertTrue(class_exists(DifferentClass::class));
    }
}
