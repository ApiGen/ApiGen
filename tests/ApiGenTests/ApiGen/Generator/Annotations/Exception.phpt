<?php

/**
 * @testCase
 * @see Project\ExceptionManager
 */

namespace ApiGenTests\ApiGen\Generator\Annotations;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class ExceptionTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->runGenerateCommand();
		Assert::true(file_exists(API_DIR . '/index.html'));

		$exceptionManagerClass = API_DIR . '/class-Project.ExceptionManager.html';
		Assert::true(file_exists($exceptionManagerClass));

		$exceptionManagerClassContent = $this->getFileContentInOneLine($exceptionManagerClass);

		Assert::match(
			'%A%<code><a href="class-RuntimeException.html">RuntimeException</a></code><br>When this happens.%A%',
			$exceptionManagerClassContent
		);
		Assert::match(
			'%A%<code><a href="class-Project.ForbiddenCallException.html">Project\ForbiddenCallException</a></code><br>This happens every time.%A%',
			$exceptionManagerClassContent
		);
		Assert::match(
			'%A%<code><a href="class-RuntimeException.html">RuntimeException</a></code><br>One comment.<br><code><a href="class-Project.ForbiddenCallException.html">Project\ForbiddenCallException</a></code><br>Another comment.%A%',
			$exceptionManagerClassContent
		);
	}

}


(new ExceptionTest)->run();
