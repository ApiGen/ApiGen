<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

class ReflectionFunctionTest extends TestCase
{

    /**
     * @var FunctionReflectionInterface
     */
    private $reflectionFunction;


    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionFunctionSource');

        $this->reflectionFunction = $backend->getFunctions()['getSomeData'];
    }


    public function testIsValid(): void
    {
        $this->assertTrue($this->reflectionFunction->isValid());
    }


    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionFunction->isDocumented());
    }


    private function getReflectionFactory(): ReflectionFactoryInterface
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
        ]);
        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}
