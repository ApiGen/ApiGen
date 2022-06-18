<?php declare(strict_types = 1);

namespace ApiGenX\Analyzer;


enum IdentifierKind
{
	case Keyword;
	case ClassLike;
	case Generic;
}
