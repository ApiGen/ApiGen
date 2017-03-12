<?php

namespace ApiGen\Configuration\Readers;

interface ReaderInterface
{

    /**
     * @return array
     */
    public function read();
}
