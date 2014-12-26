<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use TokenReflection;


class TraitsTest extends TestCase
{

	public function testIsTrait()
	{
		$this->assertFalse($this->reflectionClass->isTrait());
	}


	public function testGetTraits()
	{
		$traits = $this->reflectionClass->getTraits();
		$this->assertCount(2, $traits);
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $traits['Project\SomeTrait']);
		$this->assertSame('Project\SomeTraitNotPresentHere', $traits['Project\SomeTraitNotPresentHere']);
	}

	public function testGetOwnTraits()
	{
		$traits = $this->reflectionClass->getOwnTraits();
		$this->assertCount(2, $traits);
	}


	public function testGetTraitNames()
	{
		$this->assertSame(
			['Project\SomeTrait', 'Project\SomeTraitNotPresentHere'], $this->reflectionClass->getTraitNames()
		);
	}


	public function testGetOwnTraitName()
	{
		$this->assertSame(
			['Project\SomeTrait', 'Project\SomeTraitNotPresentHere'], $this->reflectionClass->getOwnTraitNames()
		);
	}


	public function testGetTraitAliases()
	{
		$this->assertCount(0, $this->reflectionClass->getTraitAliases());
	}


	public function testGetTraitProperties()
	{
		$this->assertCount(1, $this->reflectionClass->getTraitProperties());
	}


	public function testGetTraitMethods()
	{
		$this->assertCount(1, $this->reflectionClass->getTraitMethods());
	}


	public function testUsesTrait()
	{
		$this->assertTrue($this->reflectionClass->usesTrait('Project\SomeTrait'));
		$this->assertFalse($this->reflectionClass->usesTrait('Project\NotActiveTrait'));
	}


	/**
	 * @expectedException TokenReflection\Exception\RuntimeException
	 */
	public function testUsesTraitNotExisting()
	{
		$this->assertTrue($this->reflectionClass->usesTrait('Project\SomeTraitNotPresentHere'));
	}


	public function testGetDirectUsers()
	{
		$this->assertCount(1, $this->reflectionClassOfTrait->getDirectUsers());
	}


	public function testGetIndirectUsers()
	{
		$this->assertCount(0, $this->reflectionClassOfTrait->getIndirectUsers());
	}

}
