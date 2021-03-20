<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ElementInfo;
use Latte\Runtime\Html;
use League\CommonMark\CommonMarkConverter;
use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard as ExprPrettyPrinter;


final class LatteFunctions
{
	public function __construct(
		private UrlGenerator $url,
		private SourceHighlighter $sourceHighlighter,
		private CommonMarkConverter $commonMark,
		private ExprPrettyPrinter $exprPrettyPrinter,
	) {
	}


	public function stripHtml(string $s): string
	{
		return html_entity_decode(strip_tags($s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}


	public function highlight(string $s): Html
	{
		return new Html($this->sourceHighlighter->highlight($s));
	}


	public function prettyPrintExpr(Expr $expr): string
	{
		return $this->exprPrettyPrinter->prettyPrintExpr($expr);
	}


	public function shortDescription(string $description): string
	{
		return Strings::truncate(Strings::before("$description\n\n", "\n\n"), 120); // TODO!
	}


	public function longDescription(string $description): Html
	{
		return new Html($this->commonMark->convertToHtml($description));
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


	public function elementUrl(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->url->getClassLikeUrl($info);

		} elseif ($info instanceof NamespaceIndex) {
			return $this->url->getNamespacePath($info);

		} else {
			throw new \LogicException();
		}
	}
}
