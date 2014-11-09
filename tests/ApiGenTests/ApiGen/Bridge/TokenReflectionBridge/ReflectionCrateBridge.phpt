<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Bridge\TokenReflectionBridge;

use ApiGen\Bridge\TokenReflectionBridge\ReflectionCrateBridge;
use ApiGen\Configuration\Configuration;
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
	}


	public function testInstance()
	{
		Assert::type(
			self::CRATE_CLASS,
			$this->reflectionCrateBridge
		);

		Assert::type(
			'TokenReflection\Broker',
			$this->broker
		);
	}


	public function testClassReflectionCrossing()
	{
		$filePath = __DIR__ . DS . 'source' . DS . 'ClassToBeParsed.php';
		$this->broker->processString(file_get_contents($filePath), $filePath);
		$classes = $this->broker->getClasses(Broker\Backend::TOKENIZED_CLASSES);
		Assert::count(1, $classes);

		foreach ($classes as $class) {
			Assert::type('ApiGen\Reflection\ReflectionClass', $class);
		}
	}


	public function testFunctionReflectionCrossing()
	{
		// todo
	}


	public function testConstantReflectionCrossing()
	{
		// todo
	}

}


\run(new ReflectionCrateBridgeTest);
