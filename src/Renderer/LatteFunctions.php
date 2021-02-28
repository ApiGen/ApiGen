<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ElementInfo;
use Nette\Utils\Strings;


final class LatteFunctions
{
	public function asset(string $name): string
	{
		return "assets/$name";
	}


	public function shortDescription(string $description): string
	{
		return Strings::before("$description\n", "\n");
	}


	public function elementName(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $info->name->short;

		} elseif ($info instanceof NamespaceIndex) {
			return $info->name->full === '' ? 'none' : $info->name->full;

		} else {
			throw new \LogicException();
		}
	}


	public function elementShortDescription(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->shortDescription($info->description);

		} elseif ($info instanceof NamespaceIndex) {
			return '';

		} else {
			throw new \LogicException();
		}
	}
}
