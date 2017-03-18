<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection\ReflectionClass;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

abstract class AbstractReflectionClassTestCase extends TestCase
{

    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClassOfParent;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClassOfTrait;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClassOfInterface;


    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/../ReflectionClassSource');
        $this->reflectionClass = $backend->getClasses()['Project\AccessLevels'];
        $this->reflectionClassOfParent = $backend->getClasses()['Project\ParentClass'];
        $this->reflectionClassOfTrait = $backend->getClasses()['Project\SomeTrait'];
        $this->reflectionClassOfInterface = $backend->getClasses()['Project\RichInterface'];
    }


    private function getReflectionFactory(): Mockery\MockInterface
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserStorageMock->method('getDirectImplementersOfInterface')->willReturn([1]);
        $parserStorageMock->method('getIndirectImplementersOfInterface')->willReturn([]);
        $parserStorageMock->method('getElementsByType')->willReturnUsing(function ($arg) {
            if ($arg) {
                return [
                    'Project\AccessLevels' => $this->reflectionClass,
                    'Project\ParentClass' => $this->reflectionClassOfParent,
                    'Project\SomeTrait' => $this->reflectionClassOfTrait,
                    'Project\RichInterface' => $this->reflectionClassOfInterface
                ];
            }
        });

        $configurationMock = $this->createMock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED,
            'isInternalDocumented' => false,
            'isPhpCoreDocumented' => true,
        ]);
        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}
