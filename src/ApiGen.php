<?php

namespace ApiGen;

class ApiGen
{

    /**
     * @var string
     */
    const VERSION = '4.2.0-dev';


    /**
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
