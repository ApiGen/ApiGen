<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Configuration\Validator;


interface OptionValidatorInterface
{

	/**
	 * @param array $source
	 * @return bool
	 */
	function validateSource(array $source);


	/**
	 * @param string $destination
	 * @return bool
	 */
	function validateDestination($destination);

}
