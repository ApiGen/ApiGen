<?php

namespace ApiGen\EventDispatcher\Tests;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

abstract class ContainerAwareTestCase extends TestCase
{

    /**
     * @var Container
     */
    protected $container;


    public function __construct()
    {
        $this->container = (new ContainerFactory)->create();
    }
}
