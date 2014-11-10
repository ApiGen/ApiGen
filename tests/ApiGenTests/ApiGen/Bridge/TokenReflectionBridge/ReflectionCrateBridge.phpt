<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Bridge\TokenReflectionBridge;

use ApiGen\Bridge\TokenReflectionBridge\ReflectionCrateBridge;
use ApiGen\Configuration\Configuration;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use Tester\Assert;
use Tester\TestCase;
use TokenReflection\Broker;


require_once __DIR__ . '/../../../bootstrap.php';


class ReflectionCrateBridgeTest extends TestCase
{

	const CRATE_CLASS = 'ApiGen\Bridge\TokenReflectionBridge\ReflectionCrateBridge';

	/**
	 * @var ReflectionCrateBridge
	 */
	private $reflectionCrateBridge;

	/**
	 * @var Broker
	 */
	private $broker;


	protected function setUp()
	{
		$container = createContainer();
		$this->reflectionCrateBridge = $container->getByType(self::CRATE_CLASS);
		$this->broker = $container->getByType('TokenReflection\Broker');

		/** @var Configuration $configuration */
		$configuration = $container->getByType('ApiGen\Configuration\Configuration');
		$configuration->resolveOptions(array(
			'source' => array(__DIR__ . DS . 'source'),
			'destination' => API_DIR
		));

		$filePath = __DIR__ . DS . 'source' . DS . 'ClassToBeParsed.php';
		$this->broker->processString(file_get_contents($filePath), $filePath);
	}


	public function testInstances()
	{
		Assert::type(self::CRATE_CLASS, $this->reflectionCrateBridge);
		Assert::type('TokenReflection\Broker', $this->broker);
	}


	public function testSingleClassReflectionCrossing()
	{
		$class = $this->broker->getClass('ClassToBeParsed');
		Assert::type(
			'ApiGen\Reflection\ReflectionClass',
			$class
		);
	}


	public function testMultiClassReflectionCrossing()
	{
		$reflectionCount = 0;
		foreach ($this->broker->getClasses() as $class) {
			if ($class instanceof ReflectionClass) {
				$reflectionCount++;
			}
		}
		Assert::same(1, $reflectionCount);
	}


	public function testSingleConstantReflectionCrossing()
	{
		/** @var ReflectionClass $class */
		$class = $this->broker->getClass('ClassToBeParsed');
		$constant = $class->getConstant('LOW_PRIORITY');
		Assert::type(
			'ApiGen\Reflection\ReflectionConstant',
			$constant
		);
	}


	public function testMultiConstantReflectionCrossing()
	{
		/** @var ReflectionClass $class */
		$class = $this->broker->getClass('ClassToBeParsed');
		$reflectionCount = 0;
		foreach ($class->getConstants() as $constant) {
			if ($constant instanceof ReflectionConstant) {
				$reflectionCount++;
			}
		}
		Assert::same(1, $reflectionCount);
	}


	public function testSingleFunctionReflectionCrossing()
	{
		/** @var ReflectionClass $class */
		$class = $this->broker->getClass('ClassToBeParsed');
		$method = $class->getMethod('goOut');
		Assert::type(
			'ApiGen\Reflection\ReflectionMethod',
			$method
		);
	}


	public function testMultiFunctionReflectionCrossing()
	{
		/** @var ReflectionClass $class */
		$class = $this->broker->getClass('ClassToBeParsed');
		$reflectionCount = 0;
		foreach ($class->getMethods() as $method) {
			if ($method instanceof ReflectionMethod) {
				$reflectionCount++;
			}
		}
		Assert::same(1, $reflectionCount);
	}

}


\run(new ReflectionCrateBridgeTest);
