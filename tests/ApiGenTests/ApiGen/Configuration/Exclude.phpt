<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ExcludeTest extends TestCase
{

	public function testConfig()
	{
		$this->prepareConfig(array('*/Project/*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/namespace-Project.html'));
		Assert::false(file_exists(API_DIR . '/class-Project.Foo.html'));
		Assert::true(file_exists(API_DIR . '/namespace-ProjectBeta.html'));
		Assert::true(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->prepareConfig(array('*/ProjectBeta/*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/namespace-Project.html'));
		Assert::true(file_exists(API_DIR . '/class-Project.Foo.html'));
		Assert::false(file_exists(API_DIR . '/namespace-ProjectBeta.html'));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->prepareConfig(array('*/QueueFactory.php'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->prepareConfig(array('*/QueueFactory*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->prepareConfig(array('*Factory*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));

		$this->prepareConfig(array('*Deprecated*'));
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/class-Project.DeprecatedMethod.html'));
		Assert::false(file_exists(API_DIR . '/class-Project.Deprecated.html'));
		Assert::true(file_exists(API_DIR . '/namespace-Project.html'));
		Assert::true(file_exists(API_DIR . '/class-Project.Foo.html'));
		Assert::true(file_exists(API_DIR . '/namespace-ProjectBeta.html'));
		Assert::true(file_exists(API_DIR . '/class-ProjectBeta.QueueFactory.html'));
	}


	public function testNoneFound()
	{
		$this->prepareConfig(array('*/Project/*', '*/ProjectBeta/*'));
		passthru(APIGEN_BIN . ' generate', $output);
		Assert::same(0, $output);
		Assert::false(file_exists(API_DIR . '/index.html'));
		Assert::false(file_exists(API_DIR . '/namespace-Project.html'));
		Assert::false(file_exists(API_DIR . '/namespace-ProjectBeta.html'));

		$this->prepareConfig(array('*/Project*'));
		passthru(APIGEN_BIN . ' generate', $output);
		Assert::same(0, $output);
		Assert::false(file_exists(API_DIR . '/index.html'));
		Assert::false(file_exists(API_DIR . '/namespace-Project.html'));
		Assert::false(file_exists(API_DIR . '/namespace-ProjectBeta.html'));
	}


	/**
	 * @throws \Exception
	 */
	private function prepareConfig(array $exclude = array())
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR, PROJECT_BETA_DIR);
		$config['destination'] = API_DIR;
		if ($exclude) {
			$config['exclude'] = $exclude;
		}
		$neonFile->write($config);
	}

}


\run(new ExcludeTest);
