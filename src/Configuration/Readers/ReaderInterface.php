<?php declare(strict_types=1);

namespace ApiGen\Configuration\Readers;

interface ReaderInterface
{

    /**
     * @return array
     */
    public function read();
}
