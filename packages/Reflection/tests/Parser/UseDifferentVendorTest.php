<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use DifferentVendor\DifferentClass;
use MyVendor\MyClass;

final class UseDifferentVendorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->resolveOptions([
            'source' => [__DIR__ . '/DifferentVendorSources/src'],
            'destination' => TEMP_DIR
        ]);

        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parse();

        $this->assertTrue(class_exists(MyClass::class));
        $this->assertTrue(class_exists(DifferentClass::class));
    }
}
