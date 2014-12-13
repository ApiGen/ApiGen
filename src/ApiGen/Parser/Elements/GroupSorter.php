<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use Nette;


class GroupSorter extends Nette\Object
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
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var Elements
	 */
	private $elements;


	public function __construct(Elements $elements, Configuration $configuration)
	{
		$this->elements = $elements;
		$this->configuration = $configuration;
	}


	/**
	 * @return array
	 */
	public function sort(array $groups)
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

		$main = $this->configuration->getOption(CO::MAIN);
		uksort($this->groups, function ($one, $two) use ($main) {
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
		});

		return $this->groups;
	}


	/**
	 * @return bool
	 */
	private function isNoneGroupOnly(array $groups)
	{

		if (count($groups) === 1 && isset($groups['None'])) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param array $groupNames
	 * @return array
	 */
	private function convertGroupNamesToLower($groupNames)
	{
		$names = array_map(function ($name) {
			return strtolower($name);
		}, $groupNames);

		return array_flip($names);
	}


	/**
	 * @param string $groupName
	 */
	private function addMissingParentGroups($groupName)
	{
		$parent = '';
		foreach (explode('\\', $groupName) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');

			if ( ! isset($this->lowercasedGroupNames[strtolower($parent)])) {
				$this->groups[$parent] = $this->elements->getEmptyList();
			}
		}
	}


	/**
	 * @param string $groupName
	 */
	private function addMissingElementTypes($groupName)
	{
		foreach ($this->elements->getAll() as $type) {
			if ( ! isset($this->groups[$groupName][$type])) {
				$this->groups[$groupName][$type] = [];
			}
		}
	}

}
