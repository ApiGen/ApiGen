<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use Nette;


class GeneratorQueue extends Nette\Object
{

	/**
	 * @var TemplateGenerator[]
	 */
	private $queue;


	public function run()
	{
		foreach ($this->queue as $generator) {
			if ($generator->isAllowed()) {
				$generator->generate();
			}
		}
	}

}
