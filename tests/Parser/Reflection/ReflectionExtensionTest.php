<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionExtension;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use TokenReflection\Broker;

class ReflectionExtensionTest extends TestCase
{

    /**
     * @var ReflectionExtension
     */
    private $reflectionExtension;


    protected function setUp()
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionExtensionSource');

        /** @var ReflectionClass $reflectionClass */
        $reflectionClass = $broker->getClasses(Backend::INTERNAL_CLASSES)['Countable'];
        $this->reflectionExtension = $reflectionClass->getExtension();
    }


    public function testGetName()
    {
        $this->assertSame('SPL', $this->reflectionExtension->getName());
    }


    /**
     * @return ReflectionFactoryInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserConfiguration = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => 1,
            'isPhpCoreDocumented' => true,
            'isInternalDocumented' => false
        ]);
        return new ReflectionFactory($parserConfiguration, $parserStorageMock);
    }
}
