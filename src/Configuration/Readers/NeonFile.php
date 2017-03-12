<?php

namespace ApiGen\Configuration\Readers;

use Nette\Neon\Neon;

class NeonFile extends AbstractFile implements ReaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $json = file_get_contents($this->path);
        return (array) Neon::decode($json);
    }
}
