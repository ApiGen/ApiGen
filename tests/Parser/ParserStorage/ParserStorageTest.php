<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\ParserStorage;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\ParserStorage;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ParserStorageTest extends TestCase
{
    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;


    protected function setUp(): void
    {
        $this->parserStorage = new ParserStorage;
    }


    public function testSettersAndGetters(): void
    {
        $classes = [1];
        $this->parserStorage->setClasses($classes);
        $this->assertSame($classes, $this->parserStorage->getClasses());

        $constants = [2];
        $this->parserStorage->setConstants($constants);
        $this->assertSame($constants, $this->parserStorage->getConstants());

        $functions = [3];
        $this->parserStorage->setFunctions($functions);
        $this->assertSame($functions, $this->parserStorage->getFunctions());
    }


    public function testGetElementsByType(): void
    {
        $classes = [1];
        $this->parserStorage->setClasses($classes);
        $this->assertSame($classes, $this->parserStorage->getElementsByType(Elements::CLASSES));

        $constants = [2];
        $this->parserStorage->setConstants($constants);
        $this->assertSame($constants, $this->parserStorage->getElementsByType(Elements::CONSTANTS));

        $functions = [3];
        $this->parserStorage->setFunctions($functions);
        $this->assertSame($functions, $this->parserStorage->getElementsByType(Elements::FUNCTIONS));
    }


    /**
     * @expectedException \Exception
     */
    public function testGetElementsByTypeWithUnknownType(): void
    {
        $this->parserStorage->getElementsByType('elements');
    }


    public function testGetTypes(): void
    {
        $this->assertSame(
            [Elements::CLASSES, Elements::CONSTANTS, Elements::FUNCTIONS],
            $this->parserStorage->getTypes()
        );
    }


    public function testGetDocumentedStats(): void
    {
        $documentedStats = $this->parserStorage->getDocumentedStats();
        $this->assertInternalType('array', $documentedStats);
        $this->assertArrayHasKey('classes', $documentedStats);
        $this->assertArrayHasKey('constants', $documentedStats);
        $this->assertArrayHasKey('functions', $documentedStats);
    }


    public function testGetDocumentedElementsCount(): void
    {
        $reflectionElements = $this->getReflectionElementsArrayObject();
        $this->assertSame(1, MethodInvoker::callMethodOnObject(
            $this->parserStorage,
            'getDocumentedElementsCount',
            [$reflectionElements]
        ));
    }


    /**
     * @return ElementReflectionInterface[]
     */
    private function getReflectionElementsArrayObject(): array
    {
        $reflectionElementMock = $this->createMock(ElementReflectionInterface::class);
        $reflectionElementMock->method('isDocumented')
            ->willReturn(true);

        $reflectionElementMock2 = $this->createMock(ElementReflectionInterface::class);
        $reflectionElementMock2->method('isDocumented')
            ->willReturn(false);

        return [$reflectionElementMock, $reflectionElementMock2];
    }
}
