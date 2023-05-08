<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php71\NullableTypes;


function answer(): ?int
{
	return null;
}


function say(?string $msg)
{
	if ($msg) {
		echo $msg;
	}
}
