<?php

/**
 * @author ApiGen
 * @licence MIT
 * @copyright 2015
 */

namespace Project;

/**
 * This is some description
 *
 * @property-read int $skillCounter
 * @method string getName() This is some short description.
 * @method string doAnOperation(\stdClass $data, $type) This also some description.
 * @method static string doAStaticOperation(\stdClass $data, $type) This also some description.
 * @method static doAVoidStaticOperation(\stdClass $data, $type) This also some description.
 * @method static issue746(\stdClass $data = null, $type) This also some description.
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
    public function methodWithArgs($url = 1, $data = null, $headers = [])
    {
    }
}
