<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class UseDifferentVendorTest extends AbstractContainerAwareTestCase
{
    public function test()
    {
        $parser = $this->container->get(Parser::class);

        $parser->parseDirectories([__DIR__ . '/DifferentVendorSources/src']);

        $this->assertTrue(class_exists('\MyVendor\MyClass'));
        $this->assertTrue(class_exists('\DifferentVendor\DifferentClass'));
    }
}
