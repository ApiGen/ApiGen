<?php declare(strict_types=1);

namespace ApiGen\Contracts\Console\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface IOInterface
{
    public function getInput(): InputInterface;

    public function getOutput(): OutputInterface;

    public function writeln(string $message): void;
}
