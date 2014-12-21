<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\SelfUpdateCommand;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;


class SelfUpdateCommandTest extends ContainerAwareTestCase
{

	/**
	 * @var SelfUpdateCommand
	 */
	private $selfUpdateCommand;


	protected function setUp()
	{
		$this->selfUpdateCommand = $this->container->getByType('ApiGen\Command\SelfUpdateCommand');
	}


	public function testGetManifestItem()
	{
		$item = MethodInvoker::callMethodOnObject($this->selfUpdateCommand, 'getManifestItem');
		$this->assertInstanceOf('stdClass', $item);
		$this->assertObjectHasAttribute('name', $item);
		$this->assertObjectHasAttribute('version', $item);
		$this->assertObjectHasAttribute('sha1', $item);
		$this->assertObjectHasAttribute('version', $item);
	}


	public function testGetTempFilename()
	{
		$tempFile = MethodInvoker::callMethodOnObject($this->selfUpdateCommand, 'getTempFileName');
		$this->assertStringEndsWith('-temp.phar', $tempFile);
	}

}
