<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;
use Tester\DomQuery;


require_once __DIR__ . '/../../bootstrap.php';


class AccessLevelsTest extends TestCase
{

	public function testPublic()
	{
		$this->prepareConfig(array('public'));
		passthru(APIGEN_BIN . ' generate');
		$classFile = API_DIR . '/class-Project.AccessLevels.html';
		Assert::true(file_exists($classFile));

		$dom = DomQuery::fromHtml(file_get_contents($classFile));
		Assert::true($dom->has('table.summary.methods#methods'));
		Assert::same(1, count($dom->find('//tr')));

		Assert::match(
			'%A%public%A%',
			file_get_contents($classFile)
		);
	}


	public function testPrivate()
	{
		$this->prepareConfig(array('private'));
		passthru(APIGEN_BIN . ' generate');
		$classFile = API_DIR . '/class-Project.AccessLevels.html';
		Assert::true(file_exists($classFile));

		$dom = DomQuery::fromHtml(file_get_contents($classFile));
		Assert::true($dom->has('table.summary.methods#methods'));
		Assert::same(1, count($dom->find('//tr')));

		Assert::match(
			'%A%private%A%',
			file_get_contents($classFile)
		);
		Assert::notContains(
			'%A%public%A%',
			file_get_contents($classFile)
		);
		Assert::notContains(
			'%A%protected%A%',
			file_get_contents($classFile)
		);
	}


	public function testPublicProtectedPrivate()
	{
		$this->prepareConfig(array('public', 'private', 'protected'));
		passthru(APIGEN_BIN . ' generate');
		$classFile = API_DIR . '/class-Project.AccessLevels.html';
		Assert::true(file_exists($classFile));

		$dom = DomQuery::fromHtml(file_get_contents($classFile));
		Assert::true($dom->has('table.summary.methods#methods'));
		Assert::same(3, count($dom->find('//tr')));

		Assert::match(
			'%A%public%A%',
			file_get_contents($classFile)
		);
		Assert::match(
			'%A%protected%A%',
			file_get_contents($classFile)
		);
		Assert::match(
			'%A%protected%A%',
			file_get_contents($classFile)
		);
	}


	/**
	 * @param array $accessLevels
	 */
	private function prepareConfig($accessLevels = array())
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$config['accessLevels'] = $accessLevels;
		$neonFile->write($config);
	}

}


\run(new AccessLevelsTest);
