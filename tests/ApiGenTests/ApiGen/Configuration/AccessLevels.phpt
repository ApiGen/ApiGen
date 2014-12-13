<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;
use Tester\DomQuery;


require_once __DIR__ . '/../../bootstrap.php';


class AccessLevelsTest extends TestCase
{

	public function testPublic()
	{
		$this->runGenerateCommand('--accessLevels=public --debug');

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
		$this->runGenerateCommand('--accessLevels=private');

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
		$this->runGenerateCommand('--accessLevels=public --accessLevels=protected --accessLevels=private');

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

}


(new AccessLevelsTest)->run();
