<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen\ApiGen;
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
        ApiGen $apiGen,
        MemoryLimit $memoryLimit,
        IOInterface $io,
        DefaultInputDefinitionFactoryInterface $defaultInputDefinitionFactory
    ) {
        parent::__construct('ApiGen', $apiGen->getVersion());
        $memoryLimit->setMemoryLimitTo('1024M');
        $this->io = $io;
        $this->setDefinition($defaultInputDefinitionFactory->create());
    }


    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run($this->io->getInput(), $this->io->getOutput());
    }
}
