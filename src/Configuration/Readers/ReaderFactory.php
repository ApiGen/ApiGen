<?php declare(strict_types=1);

namespace ApiGen\Configuration\Readers;

class ReaderFactory
{

    /**
     * @var string
     */
    const NEON = 'neon';


    public static function getReader(string $path): ReaderInterface
    {
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        return ($fileExtension === self::NEON) ? new NeonFile($path) : new YamlFile($path);
    }
}
