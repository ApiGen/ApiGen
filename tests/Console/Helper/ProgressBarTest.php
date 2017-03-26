<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Helper;

use ApiGen\Console\Helper\ProgressBar;
use ApiGen\Console\Input\LiberalFormatArgvInput;
use ApiGen\Console\IO\IO;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

final class ProgressBarTest extends TestCase
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    protected function setUp(): void
    {
        $io = new IO(new LiberalFormatArgvInput, new NullOutput);
        $this->progressBar = new ProgressBar($io);
    }

    public function testInit(): void
    {
        $this->assertNull(Assert::readAttribute($this->progressBar, 'bar'));

        $this->progressBar->init(50);

        /** @var SymfonyProgressBar $bar */
        $bar = Assert::readAttribute($this->progressBar, 'bar');
        $this->assertInstanceOf(SymfonyProgressBar::class, $bar);
        $this->assertSame(50, $bar->getMaxSteps());
    }

    public function testIncrement(): void
    {
        $this->progressBar->increment();

        $this->progressBar->init(50);
        $this->progressBar->increment(20);

        /** @var SymfonyProgressBar $bar */
        $bar =  Assert::readAttribute($this->progressBar, 'bar');
        $this->assertSame(20, $bar->getProgress());

        $this->progressBar->increment(30);
        $this->assertSame(50, $bar->getProgress());
    }
}
