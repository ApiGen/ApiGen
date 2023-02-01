<?php declare(strict_types = 1);

namespace ApiGen\Info;


enum ErrorKind
{
	case SyntaxError;
	case MissingSymbol;
	case DuplicateSymbol;
}
