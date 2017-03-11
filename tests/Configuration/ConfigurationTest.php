<?php

namespace ApiGen\Configuration;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{

    /**
     * @var Configuration
     */
    private $configuration;


    protected function setUp()
    {
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class, [
            'dispatch' => '...'
        ]);
        $this->configuration = new Configuration($this->getMockedConfigurationOptionsResolver(), $eventDispatcherMock);
    }


    public function testAreNamespacesEnabled()
    {
        $this->configuration->resolveOptions(['groups' => 'namespaces']);
        $this->assertTrue($this->configuration->areNamespacesEnabled());

        $this->configuration->resolveOptions(['groups' => 'packages']);
        $this->assertFalse($this->configuration->areNamespacesEnabled());
    }


    public function testArePackagesEnabled()
    {
        $this->configuration->resolveOptions(['groups' => 'packages']);
        $this->assertTrue($this->configuration->arePackagesEnabled());

        $this->configuration->resolveOptions(['groups' => 'namespaces']);
        $this->assertFalse($this->configuration->arePackagesEnabled());
    }


    /**
     * @return Mockery\MockInterface|ConfigurationOptionsResolver
     */
    private function getMockedConfigurationOptionsResolver()
    {
        $resolver = Mockery::mock(ConfigurationOptionsResolver::class);
        $resolver->shouldReceive('resolve')
            ->withAnyArgs()
            ->andReturnUsing(function ($args) {
                return $args;
            });

        return $resolver;
    }
}
