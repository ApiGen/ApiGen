<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Elements\GroupSorter;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Parser\Tests\MethodInvoker;
use Mockery;
use Nette\Object;
use PHPUnit\Framework\TestCase;
use TokenReflection\Php\IReflection;

class ElementStorageTest extends TestCase
{

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;


    public function testEnsureCategorization(): void
    {
        $elementStorage = $this->prepareElementStorage();

        MethodInvoker::callMethodOnObject($elementStorage, 'ensureCategorization');

        $this->assertCount(1, $elementStorage->getClasses());
        $this->assertCount(1, $elementStorage->getTraits());
        $this->assertCount(1, $elementStorage->getInterfaces());
        $this->assertCount(1, $elementStorage->getExceptions());
        $this->assertCount(4, $elementStorage->getClassElements());

        $this->assertCount(1, $elementStorage->getFunctions());
        $this->assertCount(1, $elementStorage->getConstants());

        $this->assertCount(1, $elementStorage->getNamespaces());
    }


    public function testLoadUsesToReferencedElementUsedBy(): void
    {
        $elementStorage = $this->prepareElementStorage();

        $reflectionElementMock = $this->createMock(ReflectionElement::class);
        $reflectionElementMock->method('getAnnotation')->with('uses')->once()->willReturnNull();
        $reflectionElementMock->method('getAnnotation')->with('uses')->twice()->willReturn(['ApiGen\ApiGen']);
        $reflectionElementMock->method('getPrettyName')->willReturn('PrettyName');

        $this->assertFalse($this->reflectionClass->hasAnnotation('usedby'));
        MethodInvoker::callMethodOnObject(
            $elementStorage,
            'loadUsesToReferencedElementUsedBy',
            [$reflectionElementMock]
        );
        $this->assertFalse($this->reflectionClass->hasAnnotation('usedby'));

        MethodInvoker::callMethodOnObject(
            $elementStorage,
            'loadUsesToReferencedElementUsedBy',
            [$reflectionElementMock]
        );
        $this->assertTrue($this->reflectionClass->hasAnnotation('usedby'));
    }


    private function prepareElementStorage(): ElementStorage
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserStorageMock->method('getTypes')->willReturn(['classes', 'functions', 'constants']);
        $parserStorageMock->method('getElementsByType')->with('classes')
            ->willReturn($this->getReflectionClassMocks());

        $parserStorageMock->method('getElementsByType')->with('functions')
            ->willReturn([$this->getReflectionFunctionMock()]);

        $parserStorageMock->method('getElementsByType')->with('constants')
            ->willReturn([$this->getReflectionConstantMock()]);

        $groupSorterMock = $this->createMock(GroupSorter::class);
        $groupSorterMock->method('sort')->willReturnUsing(function ($elements) {
            return $elements;
        });

        $iReflectionClassMock = $this->createMock(IReflection::class, Object::class);
        $iReflectionClassMock->method('getAnnotations')->willReturn([]);

        $this->reflectionClass = new ReflectionClass($iReflectionClassMock);
        $elementResolverMock = $this->createMock(ElementResolverInterface::class);
        $elementResolverMock->method('resolveElement')->willReturn($this->reflectionClass);

        return new ElementStorage(
            $parserStorageMock,
            $groupSorterMock,
            $elementResolverMock
        );
    }


    /**
     * @return ReflectionClass[]
     */
    private function getReflectionClassMocks()
    {
        $classes = [];
        $reflectionClassMock = $this->getReflectionClassMock();
        $reflectionClassMock->method('isDocumented')->willReturn(true);
        $reflectionClassMock->method('isInterface')->willReturn(false);
        $reflectionClassMock->method('isTrait')->willReturn(false);
        $reflectionClassMock->method('isException')->willReturn(false);
        $classes[] = $reflectionClassMock;

        $reflectionClassMock2 = $this->getReflectionClassMock();
        $reflectionClassMock2->method('isDocumented')->willReturn(false);
        $classes[] = $reflectionClassMock2;

        $reflectionClassMock3 = $this->getReflectionClassMock();
        $reflectionClassMock3->method('isDocumented')->willReturn(true);
        $reflectionClassMock3->method('isInterface')->willReturn(true);
        $reflectionClassMock3->method('isTrait')->willReturn(false);
        $reflectionClassMock3->method('isException')->willReturn(false);
        $classes[] = $reflectionClassMock3;

        $reflectionClassMock4 = $this->getReflectionClassMock();
        $reflectionClassMock4->method('isDocumented')->willReturn(true);
        $reflectionClassMock4->method('isInterface')->willReturn(false);
        $reflectionClassMock4->method('isTrait')->willReturn(true);
        $reflectionClassMock4->method('isException')->willReturn(false);
        $classes[] = $reflectionClassMock4;

        $reflectionClassMock5 = $this->getReflectionClassMock();
        $reflectionClassMock5->method('isDocumented')->willReturn(true);
        $reflectionClassMock5->method('isInterface')->willReturn(false);
        $reflectionClassMock5->method('isTrait')->willReturn(false);
        $reflectionClassMock5->method('isException')->willReturn(true);
        $classes[] = $reflectionClassMock5;
        return $classes;
    }


    /**
     * @return Mockery\MockInterface|ClassReflectionInterface
     */
    private function getReflectionClassMock()
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getPseudoNamespaceName')->willReturn('SomeNamespace');
        $reflectionClassMock->method('getShortName')->willReturn('SomeShortClass');
        $reflectionClassMock->method('getOwnMethods')->willReturn([]);
        $reflectionClassMock->method('getOwnConstants')->willReturn([]);
        $reflectionClassMock->method('getOwnProperties')->willReturn([]);
        $reflectionClassMock->method('getAnnotation')->willReturn([]);
        return $reflectionClassMock;
    }


    /**
     * @return Mockery\MockInterface|FunctionReflectionInterface
     */
    private function getReflectionFunctionMock()
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('isDocumented')->willReturn(true);
        $reflectionFunctionMock->method('getPseudoNamespaceName')->willReturn('SomeNamespace');
        $reflectionFunctionMock->method('getShortName')->willReturn('SomeShortClass');
        $reflectionFunctionMock->method('getAnnotation')->willReturn([]);
        return $reflectionFunctionMock;
    }


    /**
     * @return Mockery\MockInterface|ConstantReflectionInterface
     */
    private function getReflectionConstantMock()
    {
        return $this->createMock(ConstantReflectionInterface::class, [
            'isDocumented' => true,
            'getPseudoNamespaceName' => 'SomeNamespace',
            'getShortName' => 'SomeShortClass',
            'getAnnotation' => []
        ]);
    }
}
