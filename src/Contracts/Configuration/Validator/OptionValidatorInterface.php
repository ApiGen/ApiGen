<?php

namespace ApiGen\Contracts\Configuration\Validator;

interface OptionValidatorInterface
{

    /**
     * @param array $source
     * @return bool
     */
    public function validateSource(array $source);


    /**
     * @param string $destination
     * @return bool
     */
    public function validateDestination($destination);
}
