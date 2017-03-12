<?php

namespace ApiGen\Contracts\Configuration\Validator;

interface FileValidatorInterface
{

    /**
     * @param string $path
     */
    public function validate($path);
}
