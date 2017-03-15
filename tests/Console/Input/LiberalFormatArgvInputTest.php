<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Input;

use ApiGen\Console\Input\LiberalFormatArgvInput;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class LiberalFormatArgvInputTest extends TestCase
{

    /**
     * @var LiberalFormatArgvInput
     */
    private $formatLiberalArgvInput;


    protected function setUp(): void
    {
        $inputDefinition = new InputDefinition([
            new InputOption('source', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED),
            new InputOption('destination', null, InputOption::VALUE_REQUIRED)
        ]);
        $this->formatLiberalArgvInput = new LiberalFormatArgvInput([], $inputDefinition);
    }


    public function testGetOption(): void
    {
        $this->formatLiberalArgvInput->setOption('source', ['one,two']);
        $this->assertSame(['one', 'two'], $this->formatLiberalArgvInput->getOption('source'));
    }


    public function testGetOptions(): void
    {
        $this->formatLiberalArgvInput->setOption('source', ['one,two']);
        $this->assertSame(['one', 'two'], $this->formatLiberalArgvInput->getOptions()['source']);
    }


    /**
     * @dataProvider getSplitByComma()
     */
    public function testSplitByComma($input, $expected): void
    {
        $this->assertSame(
            $expected,
            MethodInvoker::callMethodOnObject($this->formatLiberalArgvInput, 'splitByComma', [$input])
        );
    }


    /**
     * @return array[]
     */
    public function getSplitByComma()
    {
        return [
            [['one,two'], ['one', 'two']],
            ['one,two', ['one', 'two']]
        ];
    }


    /**
     * @dataProvider getRemoveEqualsData()
     */
    public function testRemoveEquals($input, $expected): void
    {
        $this->assertSame(
            $expected,
            MethodInvoker::callMethodOnObject($this->formatLiberalArgvInput, 'removeEqualsSign', [$input])
        );
    }


    /**
     * @return array[]
     */
    public function getRemoveEqualsData()
    {
        return [
            ['=something', 'something'],
            [['=something'], ['something']]
        ];
    }
}
