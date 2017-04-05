<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class SourceFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var SourceFilters
     */
    private $sourceFilters;

    protected function setUp(): void
    {
        $this->sourceFilters = $this->container->getByType(SourceFilters::class);
    }

    public function testStaticFile(): void
    {
        file_put_contents(TEMP_DIR . '/some-file.txt', '...');
        $link = $this->sourceFilters->staticFile('some-file.txt');
        $this->assertSame('some-file.txt?6eae3a5b062c6d0d79f070c26e6d62486b40cb46', $link);
    }

    public function testSourceUrlFunction(): void
    {
        $reflectionFunction = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunction->method('getName')
            ->willReturn('someFunction');
        $reflectionFunction->method('getStartLine')
            ->willReturn(15);
        $reflectionFunction->method('getEndLine')
            ->willReturn(25);

        $this->assertSame(
            'source-function-someFunction.html#15-25',
            $this->sourceFilters->sourceUrl($reflectionFunction)
        );
    }

    public function testSourceUrlClass(): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $reflectionClass->method('getName')
            ->willReturn('someClass');
        $reflectionClass->method('getStartLine')
            ->willReturn(10);
        $reflectionClass->method('getEndLine')
            ->willReturn(100);

        $this->assertSame(
            'source-class-someClass.html#10-100',
            $this->sourceFilters->sourceUrl($reflectionClass)
        );
    }

    public function testSourceUrlClassConstant(): void
    {
        $reflectionConstant = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstant->method('getName')
            ->willReturn('someConstant');
        $reflectionConstant->method('getDeclaringClassName')
            ->willReturn('someClass');
        $reflectionConstant->method('getStartLine')
            ->willReturn(20);
        $reflectionConstant->method('getEndLine')
            ->willReturn(20);

        $this->assertSame(
            'source-class-someClass.html#20',
            $this->sourceFilters->sourceUrl($reflectionConstant)
        );
    }
}
