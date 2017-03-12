<?php

namespace ApiGen\Contracts\Parser;

interface FileProcessingExceptionInterface
{

    /**
     * @return \Exception[]
     */
    public function getReasons();
}
