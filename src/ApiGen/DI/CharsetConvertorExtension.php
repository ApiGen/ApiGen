<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use Nette\DI\CompilerExtension;


class CharsetConvertorExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('charsetConvertor'))
			->setClass('ApiGen\Charset\CharsetConvertor');
	}

}
