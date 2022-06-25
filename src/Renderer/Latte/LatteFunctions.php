<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ElementInfo;
use ApiGenX\Info\EnumInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\TraitInfo;
use ApiGenX\Renderer\SourceHighlighter;
use ApiGenX\Renderer\UrlGenerator;
use Latte\Runtime\Html;
use League\CommonMark\ConverterInterface;
use Nette\Utils\Strings;

use function get_debug_type;
use function html_entity_decode;
use function sprintf;
use function strip_tags;
use function substr_count;

use const ENT_HTML5;
use const ENT_QUOTES;


class LatteFunctions
{
	public function __construct(
		private UrlGenerator $url,
		private SourceHighlighter $sourceHighlighter,
		private ConverterInterface $markdown,
	) {
	}


	public function isClass(ClassLikeInfo $info): bool
	{
		return $info instanceof ClassInfo;
	}


	public function isInterface(ClassLikeInfo $info): bool
	{
		return $info instanceof InterfaceInfo;
	}


	public function isTrait(ClassLikeInfo $info): bool
	{
		return $info instanceof TraitInfo;
	}


	public function isEnum(ClassLikeInfo $info): bool
	{
		return $info instanceof EnumInfo;
	}


	public function textWidth(string $text): int
	{
		return Strings::length($text) + 3 * substr_count($text, "\t");
	}


	public function htmlWidth(Html $html): int
	{
		$text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		return $this->textWidth($text);
	}


	public function highlight(string $s): Html
	{
		return new Html($this->sourceHighlighter->highlight($s));
	}


	public function shortDescription(string $description): string
	{
		return Strings::truncate(Strings::before($description, "\n\n") ?? $description, 120);
	}


	public function longDescription(string $description): Html
	{
		return new Html($this->markdown->convert($description)->getContent());
	}


	public function elementName(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $info->name->short;

		} elseif ($info instanceof NamespaceIndex) {
			return $info->name->full === '' ? 'none' : $info->name->full;

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}


	public function elementShortDescription(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->shortDescription($info->description);

		} elseif ($info instanceof NamespaceIndex) {
			return '';

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}


	public function elementUrl(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->url->getClassLikeUrl($info);

		} elseif ($info instanceof NamespaceIndex) {
			return $this->url->getNamespacePath($info);

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}
}
