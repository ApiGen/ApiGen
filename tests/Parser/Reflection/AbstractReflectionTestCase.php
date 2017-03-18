<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Broker\BackendInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use PHPUnit\Framework\TestCase;
use TokenReflection\Broker;

abstract class AbstractReflectionTestCase extends TestCase
{
    /**
     * @var Broker
     */
    protected $broker;

    /**
     * @var BackendInterface
     */
    protected $backend;


    protected function setUp(): void
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserConfigurationMock = $this->createMock(ConfigurationInterface::class);

        $reflectionFactory = new ReflectionFactory($parserConfigurationMock, $parserStorageMock);
        $this->backend = new Backend($reflectionFactory);
        $this->broker = new Broker($this->backend);
    }
}
