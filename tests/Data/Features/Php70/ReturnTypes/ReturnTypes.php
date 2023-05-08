<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php70\ReturnTypes;

use DateTimeInterface;


class ReturnTypes extends ReturnTypesParent
{
	public function arrayMethod(array $array): array
	{
		return $array;
	}


	public function callableMethod(callable $callable): callable
	{
		return $callable;
	}


	public function parentMethod(parent $parent): parent
	{
		return $parent;
	}


	public function selfMethod(self $self): self
	{
		return $self;
	}


	public function objectMethod(DateTimeInterface $object): DateTimeInterface
	{
		return $object;
	}
}
