<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\Elements\GroupSorterInterface;

class GroupSorter implements GroupSorterInterface
{

    /**
     * @var array
     */
    private $lowercasedGroupNames;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ElementsInterface
     */
    private $elements;


    public function __construct(ElementsInterface $elements, ConfigurationInterface $configuration)
    {
        $this->elements = $elements;
        $this->configuration = $configuration;
    }


    public function sort(array $groups): array
    {
        if ($this->isNoneGroupOnly($groups)) {
            return [];
        }
        $this->groups = $groups;

        $groupNames = array_keys($groups);
        $this->lowercasedGroupNames = $this->convertGroupNamesToLower($groupNames);

        foreach ($groupNames as $groupName) {
            $this->addMissingParentGroups($groupName);
            $this->addMissingElementTypes($groupName);
        }

        uksort($this->groups, function ($one, $two) {
            return $this->compareGroups($one, $two, $this->configuration->getMain());
        });

        return $this->groups;
    }


    private function isNoneGroupOnly(array $groups): bool
    {
        if (count($groups) === 1 && isset($groups['None'])) {
            return true;
        }
        return false;
    }


    /**
     * @param string[] $groupNames
     * @return array[]
     */
    private function convertGroupNamesToLower(array $groupNames): array
    {
        $names = array_map(function ($name) {
            return strtolower($name);
        }, $groupNames);

        return array_flip($names);
    }


    private function addMissingParentGroups(string $groupName): void
    {
        $parent = '';
        foreach (explode('\\', $groupName) as $part) {
            $parent = ltrim($parent . '\\' . $part, '\\');

            if (! isset($this->lowercasedGroupNames[strtolower($parent)])) {
                $this->groups[$parent] = $this->elements->getEmptyList();
            }
        }
    }


    private function addMissingElementTypes(string $groupName): void
    {
        foreach ($this->elements->getAll() as $type) {
            if (! isset($this->groups[$groupName][$type])) {
                $this->groups[$groupName][$type] = [];
            }
        }
    }


    private function compareGroups(string $one, string $two, string $main): int
    {
        // \ as separator has to be first
        $one = str_replace('\\', ' ', $one);
        $two = str_replace('\\', ' ', $two);

        if ($main) {
            if (strpos($one, $main) === 0 && strpos($two, $main) !== 0) {
                return -1;
            } elseif (strpos($one, $main) !== 0 && strpos($two, $main) === 0) {
                return 1;
            }
        }

        return strcasecmp($one, $two);
    }
}
