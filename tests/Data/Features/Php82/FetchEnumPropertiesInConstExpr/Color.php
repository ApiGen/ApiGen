<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php82\FetchEnumPropertiesInConstExpr;

enum Color: string
{
	case Red = '#FF0000';
	case Blue = '#0000FF';
	case Green = '#00FF00';
}
