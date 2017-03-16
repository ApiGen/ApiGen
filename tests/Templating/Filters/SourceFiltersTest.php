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
use Mockery;
use PHPUnit\Framework\TestCase;

class SourceFiltersTest extends TestCase
{

    /**
     * @var SourceFilters
     */
    private $sourceFilters;


    protected function setUp(): void
    {
        $configurationMock = Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getOption')->with('destination')->andReturn(TEMP_DIR);
        $configurationMock->shouldReceive('getOption')->with('template')->andReturn(
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
        $reflectionFunction = Mockery::mock(FunctionReflectionInterface::class);
        $reflectionFunction->shouldReceive('getName')->andReturn('someFunction');
        $reflectionFunction->shouldReceive('getStartLine')->andReturn(15);
        $reflectionFunction->shouldReceive('getEndLine')->andReturn(25);

        $this->assertSame(
            'source-file-function-someFunction.html#15-25',
            $this->sourceFilters->sourceUrl($reflectionFunction)
        );
    }


    public function testSourceUrlClass(): void
    {
        $reflectionClass = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClass->shouldReceive('getName')->andReturn('someClass');
        $reflectionClass->shouldReceive('getStartLine')->andReturn(10);
        $reflectionClass->shouldReceive('getEndLine')->andReturn(100);

        $this->assertSame(
            'source-file-class-someClass.html#10-100',
            $this->sourceFilters->sourceUrl($reflectionClass)
        );
    }


    public function testSourceUrlClassConstant(): void
    {
        $reflectionConstant = Mockery::mock(ClassConstantReflectionInterface::class);
        $reflectionConstant->shouldReceive('getName')->andReturn('someConstant');
        $reflectionConstant->shouldReceive('getDeclaringClassName')->andReturn('someClass');
        $reflectionConstant->shouldReceive('getStartLine')->andReturn(20);
        $reflectionConstant->shouldReceive('getEndLine')->andReturn(20);

        $this->assertSame(
            'source-file-class-someClass.html#20',
            $this->sourceFilters->sourceUrl($reflectionConstant)
        );
    }


    public function testSourceUrlConstant(): void
    {
        $reflectionConstant = Mockery::mock(ConstantReflectionInterface::class);
        $reflectionConstant->shouldReceive('getName')->andReturn('someConstant');
        $reflectionConstant->shouldReceive('getStartLine')->andReturn(20);
        $reflectionConstant->shouldReceive('getEndLine')->andReturn(20);

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
        $reflectionElement = Mockery::mock(ElementReflectionInterface::class, LinedInterface::class);
        $reflectionElement->shouldReceive('getName')->andReturn($name);
        $reflectionElement->shouldReceive('getStartLine')->andReturn($start);
        $reflectionElement->shouldReceive('getEndLine')->andReturn($end);
        return $reflectionElement;
    }
}
