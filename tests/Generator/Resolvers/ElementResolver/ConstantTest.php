<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use PHPUnit_Framework_MockObject_MockObject;

final class ConstantElementResolverTest extends AbstractElementResolverTest
{
    public function testGetConstant(): void
    {
        $constantReflection = $this->createConstantReflection();
        $this->parserStorage->setConstants([
            'SomeConstant' => $constantReflection
        ]);

        $this->assertInstanceOf(
            ElementReflectionInterface::class,
            $this->elementResolver->getConstant('SomeConstant')
        );
    }

    public function testGetConstantNotExisting(): void
    {
        $this->assertNull($this->elementResolver->getConstant('NotExistingConstant'));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ConstantReflectionInterface
     */
    private function createConstantReflection()
    {
        $constantReflection = $this->createMock(ConstantReflectionInterface::class);
        $constantReflection->method('isDocumented')
            ->willReturn(true);

        return $constantReflection;
    }
}
