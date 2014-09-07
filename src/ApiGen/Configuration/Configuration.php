<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use Nette;
use Nette\Utils\ArrayHash;


/**
 * @method ArrayHash getData()
 */
class Configuration extends Nette\Object
{
	/**
	 * Configuration data.
	 * @var ArrayHash
	 */
	private $data;


	public function __construct(array $parameters = array())
	{
		$this->data = new ArrayHash;
		$this->fill($parameters);
	}


	public function fillByContainer(Nette\DI\Container $container)
	{
		$this->fill($container->params);
	}


	protected function fill(array $parameters)
	{
		if ( ! empty($this->data)) {
			throw new Nette\InvalidStateException('Cannot update an already filled configuration.');
		}

		$this->data = ArrayHash::from($parameters);
//			array_map(function ($value) {
//			return is_array($value) ? new Configuration($value) : $value;
//		}, $parameters);
	}


	/**
	 * @return array
	 */
	public function toArray()
	{
		return array_map(function ($value) {
			return $value instanceof Configuration ? $value->toArray() : $value;
		}, $this->data);
	}

}
