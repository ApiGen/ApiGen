<?php declare(strict_types=1);

namespace ApiGen\Utils\Finder;

use ApiGen\Contract\Utils\Finder\FinderInterface;
use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;

final class Finder implements FinderInterface
{
    /**
     * @param string[] $sources
     * @param string[] $exclude
     * @param string[] $extensions
     * @return SplFileInfo[]
     */
    public function find(array $sources): array
    {
        $files = [];
        foreach ($sources as $source) {
            $files = array_merge($files, $this->getFilesFromSource($source));
        }

        return $files;
    }

    /**
     * @return SplFileInfo[]
     */
    private function getFilesFromSource(string $source): array
    {
        if (is_file($source)) {
            $foundFiles[$source] = new SplFileInfo($source);

            return $foundFiles;
        }

        $finder = NetteFinder::findFiles('*.php')
            ->exclude('/tests*', '/Tests*')
            ->from($source)
            ->exclude('/tests*', '/Tests*');

        return $this->convertFinderToArray($finder);
    }

    /**
     * @return SplFileInfo[]
     */
    private function convertFinderToArray(NetteFinder $finder): array
    {
        return iterator_to_array($finder->getIterator());
    }
}
