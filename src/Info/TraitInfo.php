<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class TraitInfo extends ClassLikeInfo
{
	/** @var string[] */
	public array $uses = [];


	public function __construct(string $name)
	{
		parent::__construct($name);
		$this->class = false;
		$this->interface = false;
		$this->trait = true;
	}
}
