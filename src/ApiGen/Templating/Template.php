<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use Nette;
use Nette\Utils\ObjectMixin;


/**
 * @method string   namespaceUrl()
 * @method string   packageUrl()
 * @method string   classUrl()
 * @method string   constantUrl()
 * @method string   functionUrl()
 * @method string   sourceUrl()
 * @method string   getSavePath()
 * @method Template setFile($file)
 * @method Template setSavePath()
 */
class Template extends Nette\Bridges\ApplicationLatte\Template
{

	/**
	 * @var string
	 */
	private $savePath;


	/**
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		$filters = array('namespaceUrl', 'packageUrl', 'classUrl', 'constantUrl', 'functionUrl', 'sourceUrl');
		if (in_array($name, $filters)) {
			return $this->getLatte()->invokeFilter($name, $args);
		}

		return ObjectMixin::call($this, $name, $args);
	}


	public function save()
	{
		if (file_put_contents($this->savePath, $this->__toString(TRUE)) === FALSE) {
			throw new Nette\IOException('Unable to save file to ' . $this->savePath);
		}
		$this->clear();
	}


	private function clear()
	{
//		foreach ($this->elementTypes as $type) {
//			unset($template->{'todo' . ucfirst($type)});
//		}
		// run with foreach?

		unset($this->classTree);
		unset($this->interfaceTree);
		unset($this->traitTree);
		unset($this->exceptionTree);

		unset($this->todoConstants);
		unset($this->todoMethods);
		unset($this->todoProperties);
	}

}
