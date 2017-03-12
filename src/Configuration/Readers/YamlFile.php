<?php

namespace ApiGen\Configuration\Readers;

use Symfony\Component\Yaml\Yaml;

class YamlFile extends AbstractFile implements ReaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return Yaml::parse(file_get_contents($this->path));
    }
}
