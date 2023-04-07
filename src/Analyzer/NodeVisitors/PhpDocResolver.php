<?php declare(strict_types = 1);

namespace ApiGen\Analyzer\NodeVisitors;

use ApiGen\Analyzer\IdentifierKind;
use ApiGen\Analyzer\NameContextFrame;
use ApiGen\Info\AliasReferenceInfo;
use ApiGen\Info\ClassLikeReferenceInfo;
use ApiGen\Info\Expr\ArrayExprInfo;
use ApiGen\Info\Expr\ArrayItemExprInfo;
use ApiGen\Info\Expr\BooleanExprInfo;
use ApiGen\Info\Expr\ClassConstantFetchExprInfo;
use ApiGen\Info\Expr\ConstantFetchExprInfo;
use ApiGen\Info\Expr\FloatExprInfo;
use ApiGen\Info\Expr\IntegerExprInfo;
use ApiGen\Info\Expr\NullExprInfo;
use ApiGen\Info\Expr\StringExprInfo;
use ApiGen\Info\ExprInfo;
use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFalseNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNullNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprTrueNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasImportTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeItemNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ObjectShapeItemNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

use function assert;
use function get_debug_type;
use function is_array;
use function is_object;
use function sprintf;
use function str_contains;
use function strtolower;
use function substr;


class PhpDocResolver extends NodeVisitorAbstract
{
	protected const NATIVE_KEYWORDS = [
		'array' => true,
		'bool' => true,
		'callable' => true,
		'false' => true,
		'float' => true,
		'int' => true,
		'iterable' => true,
		'mixed' => true,
		'never' => true,
		'null' => true,
		'object' => true,
		'parent' => true,
		'self' => true,
		'static' => true,
		'string' => true,
		'true' => true,
		'void' => true,
	];

	protected const KEYWORDS = self::NATIVE_KEYWORDS + [
		'array-key' => true,
		'associative-array' => true,
		'boolean' => true,
		'callable-object' => true,
		'callable-string' => true,
		'class-string' => true,
		'double' => true,
		'empty' => true,
		'integer' => true,
		'interface-string' => true,
		'key-of' => true,
		'list' => true,
		'literal-string' => true,
		'lowercase-string' => true,
		'max' => true,
		'min' => true,
		'negative-int' => true,
		'never-return' => true,
		'never-returns' => true,
		'no-return' => true,
		'non-empty-array' => true,
		'non-empty-list' => true,
		'non-empty-lowercase-string' => true,
		'non-empty-string' => true,
		'non-falsy-string' => true,
		'non-negative-int' => true,
		'non-positive-int' => true,
		'noreturn' => true,
		'number' => true,
		'numeric' => true,
		'numeric-string' => true,
		'positive-int' => true,
		'resource' => true,
		'scalar' => true,
		'trait-string' => true,
		'truthy-string' => true,
		'value-of' => true,
	];

	protected NameContextFrame $nameContextFrame;


	public function __construct(
		protected Lexer $lexer,
		protected PhpDocParser $parser,
		protected NameContext $nameContext,
	) {
		$this->nameContextFrame = new NameContextFrame(parent: null);
	}


	public function enterNode(Node $node): null|int|Node
	{
		$doc = $node->getDocComment();

		if ($doc !== null) {
			$tokens = $this->lexer->tokenize($doc->getText());
			$phpDoc = $this->parser->parse(new TokenIterator($tokens));

			if ($node instanceof Node\Stmt\ClassLike) {
				assert($node->namespacedName !== null);
				$scope = new ClassLikeReferenceInfo($node->namespacedName->toString());
				$this->nameContextFrame = $this->resolveNameContext($phpDoc, $this->nameContextFrame, $scope);

			} elseif ($node instanceof Node\FunctionLike) {
				$scope = $this->nameContextFrame->scope;
				$this->nameContextFrame = $this->resolveNameContext($phpDoc, $this->nameContextFrame, $scope);
			}

			$this->resolvePhpDoc($phpDoc);
			$node->setAttribute('phpDoc', $phpDoc);

		} elseif ($node instanceof Node\Stmt\ClassLike || $node instanceof Node\FunctionLike) {
			$this->nameContextFrame = new NameContextFrame($this->nameContextFrame);
		}

		return null;
	}


	public function leaveNode(Node $node): null|int|Node|array
	{
		if ($node instanceof Node\Stmt\ClassLike || $node instanceof Node\FunctionLike) {
			if ($this->nameContextFrame->parent === null) {
				throw new \LogicException('Name context stack is empty.');

			} else {
				$this->nameContextFrame = $this->nameContextFrame->parent;
			}
		}

		return null;
	}


	protected function resolveNameContext(PhpDocNode $doc, NameContextFrame $parent, ?ClassLikeReferenceInfo $scope): NameContextFrame
	{
		$frame = new NameContextFrame($parent);

		foreach ($doc->children as $child) {
			if ($child instanceof PhpDocTagNode) {
				if ($child->value instanceof TypeAliasTagValueNode) {
					assert($scope !== null);
					$lower = strtolower($child->value->alias);
					$frame->aliases[$lower] = new AliasReferenceInfo($scope, $child->value->alias);

				} elseif ($child->value instanceof TypeAliasImportTagValueNode) {
					$classLike = new ClassLikeReferenceInfo($this->resolveClassLikeIdentifier($child->value->importedFrom->name));
					$lower = strtolower($child->value->importedAs ?? $child->value->importedAlias);
					$frame->aliases[$lower] = new AliasReferenceInfo($classLike, $child->value->importedAlias);

				} elseif ($child->value instanceof TemplateTagValueNode) {
					$lower = strtolower($child->value->name);
					$frame->genericParameters[$lower] = true;
				}
			}
		}

		return $frame;
	}


	protected function resolvePhpDoc(PhpDocNode $phpDoc): void
	{
		$stack = [$phpDoc];
		$index = 1;

		while ($index > 0) {
			$value = $stack[--$index];

			if ($value instanceof IdentifierTypeNode) {
				$this->resolveIdentifier($value);

			} elseif ($value instanceof ConstExprNode) {
				$value->setAttribute('info', $this->resolveConstExpr($value));

			} elseif ($value instanceof ArrayShapeItemNode || $value instanceof ObjectShapeItemNode) {
				$stack[$index++] = $value->valueType; // intentionally not pushing $value->keyName

			} else {
				foreach ((array) $value as $item) {
					if (is_array($item) || is_object($item)) {
						$stack[$index++] = $item;
					}
				}
			}
		}
	}


	protected function resolveIdentifier(IdentifierTypeNode $identifier): void
	{
		$lower = strtolower($identifier->name);

		if (isset(self::KEYWORDS[$identifier->name]) || isset(self::NATIVE_KEYWORDS[$lower]) || str_contains($lower, '-')) {
			$identifier->setAttribute('kind', IdentifierKind::Keyword);

		} elseif (isset($this->nameContextFrame->genericParameters[$lower])) {
			$identifier->setAttribute('kind', IdentifierKind::Generic);

		} elseif (isset($this->nameContextFrame->aliases[$lower])) {
			$identifier->setAttribute('kind', IdentifierKind::Alias);
			$identifier->setAttribute('aliasReference', $this->nameContextFrame->aliases[$lower]);

		} else {
			$classLikeReference = new ClassLikeReferenceInfo($this->resolveClassLikeIdentifier($identifier->name));
			$identifier->setAttribute('kind', IdentifierKind::ClassLike);
			$identifier->setAttribute('classLikeReference', $classLikeReference);
		}
	}


	protected function resolveClassLikeIdentifier(string $identifier): string
	{
		if ($identifier[0] === '\\') {
			return substr($identifier, 1);

		} else {
			return $this->nameContext->getResolvedClassName(new Node\Name($identifier))->toString();
		}
	}


	protected function resolveConstExpr(ConstExprNode $expr): ExprInfo
	{
		if ($expr instanceof ConstExprTrueNode) {
			return new BooleanExprInfo(true);

		} elseif ($expr instanceof ConstExprFalseNode) {
			return new BooleanExprInfo(false);

		} elseif ($expr instanceof ConstExprNullNode) {
			return new NullExprInfo();

		} elseif ($expr instanceof ConstExprIntegerNode) {
			$node = Node\Scalar\LNumber::fromString($expr->value);
			return new IntegerExprInfo($node->value, $node->getAttribute('kind'), $expr->value);

		} elseif ($expr instanceof ConstExprFloatNode) {
			return new FloatExprInfo(Node\Scalar\DNumber::parse($expr->value), $expr->value);

		} elseif ($expr instanceof ConstExprStringNode) {
			return new StringExprInfo($expr->value, raw: null);

		} elseif ($expr instanceof ConstExprArrayNode) {
			$items = [];

			foreach ($expr->items as $item) {
				$items[] = new ArrayItemExprInfo(
					$item->key ? $this->resolveConstExpr($item->key) : null,
					$this->resolveConstExpr($item->value),
				);
			}

			return new ArrayExprInfo($items);

		} elseif ($expr instanceof ConstFetchNode) {
			if ($expr->className === '') {
				return new ConstantFetchExprInfo($expr->name);

			} else {
				return new ClassConstantFetchExprInfo(new ClassLikeReferenceInfo($this->resolveClassLikeIdentifier($expr->className)), $expr->name);
			}

		} else {
			throw new \LogicException(sprintf('Unsupported const expr node %s used in PHPDoc', get_debug_type($expr)));
		}
	}
}
