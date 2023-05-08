<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php50\FullObjectModel;


abstract class MyClassParent
{
	/** @var int */
	private $int;

	/** @var string */
	protected $string;


	/**
	 * @param int    $int
	 * @param string $string
	 */
	public function __construct($int, $string)
	{
		$this->int = $int;
		$this->string = $string;
	}


	/**
	 * @return int
	 */
	abstract protected function getNumber();
}
