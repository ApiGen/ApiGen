<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\TestCase;

class SourceFiltersTest extends TestCase
{

    /**
     * @var SourceFilters
     */
    private $sourceFilters;


    protected function setUp(): void
    {
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->method('getOption')->with('destination')->willReturn(TEMP_DIR);
        $configurationMock->method('getOption')->with('template')->willReturn(
            ['templates' => ['source' => ['filename' => 'source-file-%s.html']]
            ]
        );
            $this->sourceFilters = new SourceFilters($configurationMock);
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
        $reflectionFunction->method('getName')->willReturn('someFunction');
        $reflectionFunction->method('getStartLine')->willReturn(15);
        $reflectionFunction->method('getEndLine')->willReturn(25);

        $this->assertSame(
            'source-file-function-someFunction.html#15-25',
            $this->sourceFilters->sourceUrl($reflectionFunction)
        );
    }


    public function testSourceUrlClass(): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $reflectionClass->method('getName')->willReturn('someClass');
        $reflectionClass->method('getStartLine')->willReturn(10);
        $reflectionClass->method('getEndLine')->willReturn(100);

        $this->assertSame(
            'source-file-class-someClass.html#10-100',
            $this->sourceFilters->sourceUrl($reflectionClass)
        );
    }


    public function testSourceUrlClassConstant(): void
    {
        $reflectionConstant = $this->createMock(ClassConstantReflectionInterface::class);
        $reflectionConstant->method('getName')->willReturn('someConstant');
        $reflectionConstant->method('getDeclaringClassName')->willReturn('someClass');
        $reflectionConstant->method('getStartLine')->willReturn(20);
        $reflectionConstant->method('getEndLine')->willReturn(20);

        $this->assertSame(
            'source-file-class-someClass.html#20',
            $this->sourceFilters->sourceUrl($reflectionConstant)
        );
    }


    public function testSourceUrlConstant(): void
    {
        $reflectionConstant = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstant->method('getName')->willReturn('someConstant');
        $reflectionConstant->method('getStartLine')->willReturn(20);
        $reflectionConstant->method('getEndLine')->willReturn(20);

        $this->assertSame(
            'source-file-constant-someConstant.html#20',
            $this->sourceFilters->sourceUrl($reflectionConstant)
        );
    }


	public function testGetElementLinesAnchor()
	{
		$elementMock = $this->buildReflectionElement(NULL, 20, 40);
		$this->assertSame(
			'#20-40',
			MethodInvoker::callMethodOnObject($this->sourceFilters, 'getElementLinesAnchor', [$elementMock])
		);

		$elementMock = $this->buildReflectionElement(NULL, 20, 20);
		$this->assertSame(
			'#20',
			MethodInvoker::callMethodOnObject($this->sourceFilters, 'getElementLinesAnchor', [$elementMock])
		);
	}


    private function buildReflectionElement(string $name, int $start, int $end): Mockery\MockInterface
    {
        $reflectionElement = $this->createMock(ElementReflectionInterface::class, LinedInterface::class);
        $reflectionElement->method('getName')->willReturn($name);
        $reflectionElement->method('getStartLine')->willReturn($start);
        $reflectionElement->method('getEndLine')->willReturn($end);
        return $reflectionElement;
    }
}
