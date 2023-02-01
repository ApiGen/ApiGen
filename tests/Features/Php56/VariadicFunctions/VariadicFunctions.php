<?php declare(strict_types = 1);

namespace ApiGenTests\Features\Php56\VariadicFunctions;

use DateTimeInterface;


class VariadicFunctions
{
	public function queryA($query, ...$params)
	{
	}


	public function queryB($query, &...$params)
	{
	}


	public function queryC($query, DateTimeInterface ...$params)
	{
	}


	public function queryD($query, DateTimeInterface &...$params)
	{
	}
}
