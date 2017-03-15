<?php declare(strict_types=1);

namespace ApiGen\Console;

use ApiGen\Contracts\Console\Input\DefaultInputDefinitionFactoryInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\MemoryLimit;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{

    /**
     * @var IOInterface
     */
    private $io;


    public function __construct(
        IOInterface $io,
        DefaultInputDefinitionFactoryInterface $defaultInputDefinitionFactory
    ) {
        parent::__construct('ApiGen');
        $this->io = $io;
        $this->setDefinition($defaultInputDefinitionFactory->create());
    }


    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run($this->io->getInput(), $this->io->getOutput());
    }
}
