<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Charset\CharsetDetector;
use ApiGen\Charset\Configuration\CharsetOptionsResolver;
use Nette\DI\CompilerExtension;


class CharsetConvertorExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('convertor'))
			->setClass(CharsetConvertor::class);

		$builder->addDefinition($this->prefix('detector'))
			->setClass(CharsetDetector::class);

		$builder->addDefinition($this->prefix('charsetOptionsResolver'))
			->setClass(CharsetOptionsResolver::class);
	}

}
