<?php

namespace Project;


/**
 * @property-read int $skillCounter
 * @method string getName() This is some short description.
 *
 * @package Some_Package
 */
class ReflectionMethod
{

	/**
	 * @param int
	 */
	public $memberCount = 52;


	/**
	 * Send a POST request
	 *
	 * @param int|string $url the URL of the API endpoint
	 * @param mixed $data and array or a blob of data to be sent
	 * @param array $headers add optional headers
	 */
	public function methodWithArgs($url = 1, $data = NULL, $headers = [])
	{
	}

}
