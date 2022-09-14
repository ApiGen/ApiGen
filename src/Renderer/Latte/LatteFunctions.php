<?php declare(strict_types = 1);

namespace ApiGen\Renderer\Latte;

use ApiGen\Index\NamespaceIndex;
use ApiGen\Info\ClassInfo;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Info\ElementInfo;
use ApiGen\Info\EnumInfo;
use ApiGen\Info\FunctionInfo;
use ApiGen\Info\InterfaceInfo;
use ApiGen\Info\TraitInfo;
use ApiGen\Renderer\Filter;
use ApiGen\Renderer\SourceHighlighter;
use ApiGen\Renderer\UrlGenerator;
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
		protected Filter $filter,
		protected UrlGenerator $url,
		protected SourceHighlighter $sourceHighlighter,
		protected ConverterInterface $markdown,
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


	public function highlight(string $path): Html
	{
		return new Html($this->sourceHighlighter->highlight($path));
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
		if ($info instanceof ClassLikeInfo || $info instanceof FunctionInfo) {
			return $info->name->short;

		} elseif ($info instanceof NamespaceIndex) {
			return $info->name->full === '' ? 'none' : $info->name->full;

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}


	public function elementShortDescription(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo || $info instanceof FunctionInfo) {
			return $this->shortDescription($info->description);

		} elseif ($info instanceof NamespaceIndex) {
			return '';

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}


	public function elementPageExists(ElementInfo $info): bool
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->filter->filterClassLikePage($info);

		} elseif ($info instanceof NamespaceIndex) {
			return $this->filter->filterNamespacePage($info);

		} elseif ($info instanceof FunctionInfo) {
			return $this->filter->filterFunctionPage($info);

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}


	public function elementUrl(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->url->getClassLikeUrl($info);

		} elseif ($info instanceof NamespaceIndex) {
			return $this->url->getNamespaceUrl($info);

		} elseif ($info instanceof FunctionInfo) {
			return $this->url->getFunctionUrl($info);

		} else {
			throw new \LogicException(sprintf('Unexpected element type %s', get_debug_type($info)));
		}
	}
}
