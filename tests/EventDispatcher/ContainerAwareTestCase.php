<?php

namespace ApiGen\EventDispatcher\Tests;

use Nette\DI\Container;
use PHPUnit_Framework_TestCase;


abstract class ContainerAwareTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Container
	 */
	protected $container;


	public function __construct()
	{
		$this->container = (new ContainerFactory)->create();
	}

}
