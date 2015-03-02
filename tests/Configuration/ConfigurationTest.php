<?php

namespace ApiGen\Configuration;

use Mockery;
use PHPUnit_Framework_TestCase;


class ConfigurationTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;


	protected function setUp()
	{
		$this->configuration = new Configuration($this->getMockedConfigurationOptionsResolver());
	}


	public function testAreNamespacesEnabled()
	{
		$this->configuration->resolveOptions(['groups' => 'auto']);
		$this->assertTrue($this->configuration->areNamespacesEnabled(5, 0));
		$this->assertFalse($this->configuration->areNamespacesEnabled(0, 5));
		$this->assertTrue($this->configuration->areNamespacesEnabled(5, 5));

		$this->configuration->resolveOptions(['groups' => 'namespaces']);
		$this->assertTrue($this->configuration->areNamespacesEnabled(5, 0));
		$this->assertTrue($this->configuration->areNamespacesEnabled(0, 5));
		$this->assertTrue($this->configuration->areNamespacesEnabled(5, 5));

		$this->configuration->resolveOptions(['groups' => 'packages']);
		$this->assertFalse($this->configuration->areNamespacesEnabled(5, 0));
		$this->assertFalse($this->configuration->areNamespacesEnabled(0, 5));
		$this->assertFalse($this->configuration->areNamespacesEnabled(5, 5));
	}


	public function testArePackagesEnabled()
	{
		$this->configuration->resolveOptions(['groups' => 'auto']);
		$this->assertFalse($this->configuration->arePackagesEnabled(TRUE));
		$this->assertTrue($this->configuration->arePackagesEnabled(FALSE));

		$this->configuration->resolveOptions(['groups' => 'namespaces']);
		$this->assertFalse($this->configuration->arePackagesEnabled(TRUE));
		$this->assertFalse($this->configuration->arePackagesEnabled(FALSE));

		$this->configuration->resolveOptions(['groups' => 'packages']);
		$this->assertTrue($this->configuration->arePackagesEnabled(TRUE));
		$this->assertTrue($this->configuration->arePackagesEnabled(FALSE));
	}


	/**
	 * @return Mockery\MockInterface|ConfigurationOptionsResolver
	 */
	private function getMockedConfigurationOptionsResolver()
	{
		$resolver = Mockery::mock(ConfigurationOptionsResolver::class);
		$resolver->shouldReceive('resolve')
			->withAnyArgs()
			->andReturnUsing(function($args) {
				return $args;
			});

		return $resolver;
	}

}
