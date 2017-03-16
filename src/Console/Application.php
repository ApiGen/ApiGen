<?php declare(strict_types=1);

namespace ApiGen\Console;

use ApiGen\Contracts\Console\Input\DefaultInputDefinitionFactoryInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Application extends BaseApplication
{
    /**
     * @var string
     */
    private const NAME = 'ApiGen';

    /**
     * @var IOInterface
     */
    private $io;


    public function __construct(
        IOInterface $io,
        DefaultInputDefinitionFactoryInterface $defaultInputDefinitionFactory
    ) {
        parent::__construct(self::NAME);
        $this->io = $io;
        $this->setDefinition($defaultInputDefinitionFactory->create());
    }


    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        return parent::run($this->io->getInput(), $this->io->getOutput());
    }
}
