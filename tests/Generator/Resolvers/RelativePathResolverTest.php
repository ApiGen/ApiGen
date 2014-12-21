<?php

namespace ApiGen\Tests\Generator\Resolvers;

use ApiGen\Generator\Resolvers\RelativePathResolver;
use PHPUnit_Framework_TestCase;


class RelativePathResolverTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Issue #408
	 */
	public function testEndingSlash()
	{
		$relativePathResolver = new RelativePathResolver;
		$config = [
			'source' => ['ProjectBeta/']
		];
		$relativePathResolver->setConfig($config);
		$fileName = 'ProjectBeta/entities/Category.php';
		$relativePath = $relativePathResolver->getRelativePath($fileName);

		$this->assertSame('entities/Category.php', $relativePath);

		$config = [
			'source' => ['ProjectBeta']
		];
		$relativePathResolver->setConfig($config);
		$fileName = 'ProjectBeta/entities/Category.php';
		$relativePath2 = $relativePathResolver->getRelativePath($fileName);
		$this->assertSame($relativePath, $relativePath2);
	}

}
