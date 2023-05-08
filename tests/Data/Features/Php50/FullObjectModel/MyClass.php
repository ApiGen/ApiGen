<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php50\FullObjectModel;


final class MyClass extends MyClassParent implements MyInterface
{
	const A = 1;

	/** @var int */
	public static $aStatic = 1;

	/** @var string|null */
	protected static $bStatic = null;

	/** @var MyInterface */
	private static $cStatic;

	/** @var int */
	public $a = 1;

	/** @var string|null */
	protected $b = null;

	/** @var MyInterface */
	private $c;


	/**
	 * @param int         $a
	 * @param string|null $b
	 * @param MyInterface $c
	 */
	public function __construct($a, $b, MyInterface $c)
	{
		parent::__construct(7, 'string');

		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
	}


	/**
	 * @return self
	 */
	public static function createFromInterface(MyInterface $c)
	{
		return new self(self::A, $c->getName(), $c);
	}


	final public function a(MyInterface $c, $a = MyClass::A)
	{
		$this->c = $c;
		$this->a = $a;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->b ? $this->b : 'default';
	}


	/**
	 * @return int
	 */
	protected function getNumber()
	{
		return $this->a;
	}


	/**
	 * @return int
	 */
	private function getRandomNumber()
	{
		return 123;
	}
}
