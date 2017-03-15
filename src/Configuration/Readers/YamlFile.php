<?php declare(strict_types=1);

namespace ApiGen\Configuration\Readers;

use Symfony\Component\Yaml\Yaml;

class YamlFile extends AbstractFile implements ReaderInterface
{

    public function read(): array
    {
        return Yaml::parse(file_get_contents($this->path));
    }
}
