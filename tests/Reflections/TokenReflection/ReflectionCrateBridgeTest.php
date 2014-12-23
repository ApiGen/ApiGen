<?php

namespace ApiGen\Tests\Reflection\TokenReflection;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\TokenReflection\ReflectionCrateBridge;
use Mockery;
use PHPUnit_Framework_TestCase;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionFunction;


class ReflectionCrateBridgeTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionCrateBridge
	 */
	private $reflectionCrateBridge;


	protected function setUp()
	{
		$reflectionFactoryMock = Mockery::mock('ApiGen\Reflection\TokenReflection\ReflectionFactory');
		$reflectionFactoryMock->shouldReceive('createFromReflection')->andReturnUsing(function ($arg) {
			if ($arg instanceof IReflectionClass) {
				return new ReflectionClass($arg);

			} elseif ($arg instanceof IReflectionConstant) {
				return new ReflectionConstant($arg);

			} elseif ($arg instanceof IReflectionFunction) {
				return new ReflectionFunction($arg);

			} else {
				return $arg;
			}
		});
		$this->reflectionCrateBridge = new ReflectionCrateBridge($reflectionFactoryMock);
	}


	public function testCrossClassReflections()
	{
		$classReflectionMocks = [Mockery::mock('TokenReflection\IReflectionClass', 'Nette\Object')];
		$classReflections = $this->reflectionCrateBridge->crossClassReflections($classReflectionMocks);
		$classReflection = $classReflections[0];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $classReflection);
		$this->assertNotInstanceOf('TokenReflection\IReflectionClass', $classReflection);
	}


	public function testCrossConstantReflections()
	{
		$constantReflectionMocks = [Mockery::mock('TokenReflection\IReflectionConstant', 'Nette\Object')];
		$constantReflections = $this->reflectionCrateBridge->crossConstantReflections($constantReflectionMocks);
		$constantReflection = $constantReflections[0];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $constantReflection);
		$this->assertNotInstanceOf('TokenReflection\IReflectionConstant', $constantReflection);

	}


	public function testCrossFunctionReflections()
	{
		$functionReflectionMocks = [Mockery::mock('TokenReflection\IReflectionFunction', 'Nette\Object')];
		$functionReflections = $this->reflectionCrateBridge->crossFunctionReflections($functionReflectionMocks);
		$functionReflection = $functionReflections[0];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionFunction', $functionReflection);
		$this->assertNotInstanceOf('TokenReflection\IReflectionFunction', $functionReflection);
	}

}
