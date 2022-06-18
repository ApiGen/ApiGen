<?php declare(strict_types = 1);

namespace ApiGenX\Info;


enum GenericParameterVariance
{
	case Invariant;
	case Covariant;
	case Contravariant;
}
