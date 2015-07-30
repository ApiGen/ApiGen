<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Broker\BackendInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;

abstract class AbstractReflectionTestCase extends PHPUnit_Framework_TestCase
{

    /**
     * @var Broker
     */
    protected $broker;

    /**
     * @var BackendInterface
     */
    protected $backend;


    protected function setUp()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserConfigurationMock = Mockery::mock(ConfigurationInterface::class);

        $reflectionFactory = new ReflectionFactory($parserConfigurationMock, $parserStorageMock);
        $this->backend = new Backend($reflectionFactory);
        $this->broker = new Broker($this->backend);
    }
}
