<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator\Resolvers;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use Nette\DI\Container;
use Tester\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class RelativePathResolverTest extends TestCase
{

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;


	protected function setUp()
	{
		$this->container = createContainer();
		$this->relativePathResolver = $this->container->getByType('ApiGen\Generator\Resolvers\RelativePathResolver');
	}


	public function testInstance()
	{
		Assert::type(
			'ApiGen\Generator\Resolvers\RelativePathResolver',
			$this->relativePathResolver
		);
	}


	/**
	 * Issue #408
	 */
	public function testEndingSlash()
	{
		/** @var Configuration $configuration */
		$configuration = $this->container->getByType('ApiGen\Configuration\Configuration');

		$fileName = PROJECT_BETA_DIR . '/entities/Category.php';

		$configuration->setValues(array(
			'source' => array(PROJECT_BETA_DIR . DS),
			'destination' => API_DIR
		));
		$relativePath = $this->relativePathResolver->getRelativePath($fileName);
		Assert::same('entities/Category.php', $relativePath);

		$configuration->setValues(array(
			'source' => array(PROJECT_BETA_DIR),
			'destination' => API_DIR
		));
		$relativePath2 = $this->relativePathResolver->getRelativePath($fileName);
		Assert::same($relativePath2, $relativePath);
	}

}


\run(new RelativePathResolverTest);
