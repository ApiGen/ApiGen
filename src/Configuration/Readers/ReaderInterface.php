<?php declare(strict_types=1);

namespace ApiGen\Configuration\Readers;

interface ReaderInterface
{

    public function read(): array;
}
