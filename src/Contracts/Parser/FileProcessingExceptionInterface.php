<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser;

interface FileProcessingExceptionInterface
{

    /**
     * @return \Exception[]
     */
    public function getReasons();
}
