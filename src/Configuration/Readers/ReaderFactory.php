<?php

namespace ApiGen\Configuration\Readers;

class ReaderFactory
{

    /**
     * @var string
     */
    const NEON = 'neon';


    /**
     * @param string $path
     * @return ReaderInterface
     */
    public static function getReader($path)
    {
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        return ($fileExtension === self::NEON) ? new NeonFile($path) : new YamlFile($path);
    }
}
