<?php

namespace Project;


/**
 * @property $someMagicProperty
 * @method getSome()
 */
class AccessLevels extends ParentClass
{

	use SomeTrait;

	const LEVEL = 5;

	public $publicProperty;

	protected $protectedProperty;

	private $privateProperty;


	public function publicMethod()
	{
	}


	protected function protectedMethod()
	{
	}


	private function privateMethod()
	{
	}

}
