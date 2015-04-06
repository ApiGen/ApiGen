<?php

namespace ApiGen\Tests\Reflections\ReflectionClass;

use ApiGen\Reflection\ReflectionClass;
use Project\SomeTrait;
use TokenReflection;
use TokenReflection\Exception\RuntimeException;


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
		$this->assertInstanceOf(ReflectionClass::class, $traits[SomeTrait::class]);
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
			[SomeTrait::class, 'Project\SomeTraitNotPresentHere'], $this->reflectionClass->getTraitNames()
		);
	}


	public function testGetOwnTraitName()
	{
		$this->assertSame(
			[SomeTrait::class, 'Project\SomeTraitNotPresentHere'], $this->reflectionClass->getOwnTraitNames()
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
		$this->assertTrue($this->reflectionClass->usesTrait(SomeTrait::class));
		$this->assertFalse($this->reflectionClass->usesTrait('Project\NotActiveTrait'));
	}


	public function testUsesTraitNotExisting()
	{
		$this->setExpectedException(RuntimeException::class);
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
