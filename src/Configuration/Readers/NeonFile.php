<?php declare(strict_types=1);

namespace ApiGen\Configuration\Readers;

use Nette\Neon\Neon;

class NeonFile extends AbstractFile implements ReaderInterface
{
    /**
     * @return mixed[]
     */
    public function read(): array
    {
        $json = file_get_contents($this->path);
        return (array) Neon::decode($json);
    }
}
