<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\IO;

use ApiGen\Console\Input\LiberalFormatArgvInput;
use ApiGen\Console\IO\IO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class IOTest extends TestCase
{
    public function testGetters(): void
    {
        $io = new IO(new LiberalFormatArgvInput, new NullOutput);

        $this->assertInstanceOf(InputInterface::class, $io->getInput());
        $this->assertInstanceOf(OutputInterface::class, $io->getOutput());
    }
}
