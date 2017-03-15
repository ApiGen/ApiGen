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


    public function testEnsureCategorization()
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


    public function testLoadUsesToReferencedElementUsedBy()
    {
        $elementStorage = $this->prepareElementStorage();

        $reflectionElementMock = Mockery::mock(ReflectionElement::class);
        $reflectionElementMock->shouldReceive('getAnnotation')->with('uses')->once()->andReturnNull();
        $reflectionElementMock->shouldReceive('getAnnotation')->with('uses')->twice()->andReturn(['ApiGen\ApiGen']);
        $reflectionElementMock->shouldReceive('getPrettyName')->andReturn('PrettyName');

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


    /**
     * @return ElementStorage
     */
    private function prepareElementStorage()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserStorageMock->shouldReceive('getTypes')->andReturn(['classes', 'functions', 'constants']);
        $parserStorageMock->shouldReceive('getElementsByType')->with('classes')
            ->andReturn($this->getReflectionClassMocks());

        $parserStorageMock->shouldReceive('getElementsByType')->with('functions')
            ->andReturn([$this->getReflectionFunctionMock()]);

        $parserStorageMock->shouldReceive('getElementsByType')->with('constants')
            ->andReturn([$this->getReflectionConstantMock()]);

        $groupSorterMock = Mockery::mock(GroupSorter::class);
        $groupSorterMock->shouldReceive('sort')->andReturnUsing(function ($elements) {
            return $elements;
        });

        $iReflectionClassMock = Mockery::mock(IReflection::class, Object::class);
        $iReflectionClassMock->shouldReceive('getAnnotations')->andReturn([]);

        $this->reflectionClass = new ReflectionClass($iReflectionClassMock);
        $elementResolverMock = Mockery::mock(ElementResolverInterface::class);
        $elementResolverMock->shouldReceive('resolveElement')->andReturn($this->reflectionClass);

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
        $reflectionClassMock->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock->shouldReceive('isException')->andReturn(false);
        $classes[] = $reflectionClassMock;

        $reflectionClassMock2 = $this->getReflectionClassMock();
        $reflectionClassMock2->shouldReceive('isDocumented')->andReturn(false);
        $classes[] = $reflectionClassMock2;

        $reflectionClassMock3 = $this->getReflectionClassMock();
        $reflectionClassMock3->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock3->shouldReceive('isInterface')->andReturn(true);
        $reflectionClassMock3->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock3->shouldReceive('isException')->andReturn(false);
        $classes[] = $reflectionClassMock3;

        $reflectionClassMock4 = $this->getReflectionClassMock();
        $reflectionClassMock4->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock4->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock4->shouldReceive('isTrait')->andReturn(true);
        $reflectionClassMock4->shouldReceive('isException')->andReturn(false);
        $classes[] = $reflectionClassMock4;

        $reflectionClassMock5 = $this->getReflectionClassMock();
        $reflectionClassMock5->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock5->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock5->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock5->shouldReceive('isException')->andReturn(true);
        $classes[] = $reflectionClassMock5;
        return $classes;
    }


    /**
     * @return Mockery\MockInterface|ClassReflectionInterface
     */
    private function getReflectionClassMock()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('getPseudoNamespaceName')->andReturn('SomeNamespace');
        $reflectionClassMock->shouldReceive('getShortName')->andReturn('SomeShortClass');
        $reflectionClassMock->shouldReceive('getOwnMethods')->andReturn([]);
        $reflectionClassMock->shouldReceive('getOwnConstants')->andReturn([]);
        $reflectionClassMock->shouldReceive('getOwnProperties')->andReturn([]);
        $reflectionClassMock->shouldReceive('getAnnotation')->andReturn([]);
        return $reflectionClassMock;
    }


    /**
     * @return Mockery\MockInterface|FunctionReflectionInterface
     */
    private function getReflectionFunctionMock()
    {
        $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->shouldReceive('isDocumented')->andReturn(true);
        $reflectionFunctionMock->shouldReceive('getPseudoNamespaceName')->andReturn('SomeNamespace');
        $reflectionFunctionMock->shouldReceive('getShortName')->andReturn('SomeShortClass');
        $reflectionFunctionMock->shouldReceive('getAnnotation')->andReturn([]);
        return $reflectionFunctionMock;
    }


    /**
     * @return Mockery\MockInterface|ConstantReflectionInterface
     */
    private function getReflectionConstantMock()
    {
        return Mockery::mock(ConstantReflectionInterface::class, [
            'isDocumented' => true,
            'getPseudoNamespaceName' => 'SomeNamespace',
            'getShortName' => 'SomeShortClass',
            'getAnnotation' => []
        ]);
    }
}
