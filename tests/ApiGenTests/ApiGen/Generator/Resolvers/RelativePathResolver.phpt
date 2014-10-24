<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator\Resolvers;

use ApiGen\Generator\Resolvers\RelativePathResolver;
use Tester\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class RelativePathResolverTest extends TestCase
{

	/**
	 * Issue #408
	 */
	public function testEndingSlash()
	{
		$relativePathResolver = new RelativePathResolver;
		$config = array(
			'source' => array('ProjectBeta/')
		);
		$relativePathResolver->setConfig($config);
		$fileName = 'ProjectBeta/entities/Category.php';
		$relativePath = $relativePathResolver->getRelativePath($fileName);
		Assert::same('entities/Category.php', $relativePath);

		$config = array(
			'source' => array('ProjectBeta')
		);
		$relativePathResolver->setConfig($config);
		$fileName = 'ProjectBeta/entities/Category.php';
		$relativePath2 = $relativePathResolver->getRelativePath($fileName);
		Assert::same($relativePath, $relativePath2);
	}

}


\run(new RelativePathResolverTest);
