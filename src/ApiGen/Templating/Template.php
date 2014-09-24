<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use Nette;


/**
 * Customized ApiGen template class.
 *
 * @method Template setFile($file)
 * @method string   namespaceUrl(string $s)
 * @method string   classUrl(string $s)
 * @method string   constantUrl(string $s)
 * @method string   functionUrl(string $s)
 * @method string   sourceUrl(string $s)
 */
class Template extends Nette\Bridges\ApplicationLatte\Template
{

	/**
	 * Renders template to file.
	 *
	 * @param string $file
	 */
	public function save($file)
	{
		if (file_put_contents($file, $this->__toString(TRUE)) === FALSE) {
			throw new Nette\IOException("Unable to save file '$file'.");
		}
	}


	/**
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		$filters = array('namespaceUrl', 'classUrl', 'constantUrl', 'functionUrl', 'sourceUrl');
		if (in_array($name, $filters)) {
			return $this->getLatte()->invokeFilter($name, $args);
		}

		return parent::__call($name, $args);
	}

}
