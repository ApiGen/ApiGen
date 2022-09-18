<?php declare(strict_types = 1);

namespace ApiGen\Analyzer\NodeVisitors;

use ApiGen\Analyzer\IdentifierKind;
use ApiGen\Info\ClassLikeReferenceInfo;
use ApiGen\Info\GenericParameterInfo;
use ApiGen\Info\GenericParameterVariance;
use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ImplementsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MixinTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\UsesTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConditionalTypeForParameterNode;
use PHPStan\PhpDocParser\Ast\Type\ConditionalTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\OffsetAccessTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

use function array_pop;
use function assert;
use function count;
use function get_class;
use function str_contains;
use function str_ends_with;
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
		'callable-string' => true,
		'class-string' => true,
		'double' => true,
		'integer' => true,
		'interface-string' => true,
		'key-of' => true,
		'list' => true,
		'literal-string' => true,
		'negative-int' => true,
		'never-return' => true,
		'never-returns' => true,
		'no-return' => true,
		'non-empty-array' => true,
		'non-empty-list' => true,
		'non-empty-string' => true,
		'noreturn' => true,
		'number' => true,
		'numeric' => true,
		'numeric-string' => true,
		'positive-int' => true,
		'resource' => true,
		'scalar' => true,
		'trait-string' => true,
		'value-of' => true,
	];

	/** @var GenericParameterInfo[][] indexed by [][parameterName] */
	protected array $genericNameContextStack = [];


	public function __construct(
		protected Lexer $lexer,
		protected PhpDocParser $parser,
		protected NameContext $nameContext,
	) {
	}


	public function enterNode(Node $node): null|int|Node
	{
		$doc = $node->getDocComment();

		if ($doc !== null) {
			$tokens = $this->lexer->tokenize($doc->getText());
			$phpDoc = $this->parser->parse(new TokenIterator($tokens));

			if ($node instanceof Node\Stmt\ClassLike || $node instanceof Node\FunctionLike) {
				$genericNameContext = $this->resolveGenericNameContext($phpDoc);
				$phpDoc->setAttribute('genericNameContext', $genericNameContext);
				$this->genericNameContextStack[] = $genericNameContext;
			}

			$this->resolvePhpDoc($phpDoc);
			$node->setAttribute('phpDoc', $phpDoc);

		} elseif ($node instanceof Node\Stmt\ClassLike || $node instanceof Node\FunctionLike) {
			$this->genericNameContextStack[] = [];
		}

		return null;
	}


	public function leaveNode(Node $node): null|int|Node|array
	{
		if ($node instanceof Node\Stmt\ClassLike || $node instanceof Node\FunctionLike) {
			if (array_pop($this->genericNameContextStack) === null) {
				throw new \LogicException();
			}
		}

		return null;
	}


	/**
	 * @return GenericParameterInfo[] indexed by [parameterName]
	 */
	protected function resolveGenericNameContext(PhpDocNode $doc): array
	{
		$context = [];

		foreach ($doc->children as $child) {
			if ($child instanceof PhpDocTagNode && $child->value instanceof TemplateTagValueNode) {
				$lower = strtolower($child->value->name);
				$variance = str_ends_with($child->name, '-covariant') ? GenericParameterVariance::Covariant : GenericParameterVariance::Invariant;
				$context[$lower] = new GenericParameterInfo($child->value->name, $variance, $child->value->bound, $child->value->description);
			}
		}

		return $context;
	}


	/**
	 * @return iterable<TypeNode>
	 */
	public static function getTypes(PhpDocNode $phpDocNode): iterable
	{
		foreach ($phpDocNode->getTags() as $tag) {
			switch (get_class($tag->value)) {
				case ParamTagValueNode::class:
				case PropertyTagValueNode::class:
				case ReturnTagValueNode::class:
				case ThrowsTagValueNode::class:
				case VarTagValueNode::class:
				case ExtendsTagValueNode::class:
				case ImplementsTagValueNode::class:
				case UsesTagValueNode::class:
				case MixinTagValueNode::class:
					yield $tag->value->type;
					break;

				case MethodTagValueNode::class:
					if ($tag->value->returnType !== null) {
						yield $tag->value->returnType;
					}

					foreach ($tag->value->parameters as $parameter) {
						if ($parameter->type !== null) {
							yield $parameter->type;
						}
					}
					break;
			}
		}

		foreach ($phpDocNode->getAttribute('genericNameContext') ?? [] as $genericParameter) {
			assert($genericParameter instanceof GenericParameterInfo);

			if ($genericParameter->bound !== null) {
				yield $genericParameter->bound;
			}
		}
	}


	/**
	 * @return iterable<ConstExprNode>
	 */
	public static function getExpressions(PhpDocNode $phpDocNode): iterable
	{
		foreach ($phpDocNode->getTags() as $tag) {
			if ($tag->value instanceof MethodTagValueNode) {
				foreach ($tag->value->parameters as $parameter) {
					if ($parameter->defaultValue) {
						yield $parameter->defaultValue;
					}
				}
			}
		}
	}


	/**
	 * @return iterable<IdentifierTypeNode>
	 */
	public static function getIdentifiers(TypeNode $typeNode): iterable
	{
		if ($typeNode instanceof IdentifierTypeNode) {
			yield $typeNode;

		} elseif ($typeNode instanceof NullableTypeNode || $typeNode instanceof ArrayTypeNode) {
			yield from self::getIdentifiers($typeNode->type);

		} elseif ($typeNode instanceof UnionTypeNode || $typeNode instanceof IntersectionTypeNode) {
			foreach ($typeNode->types as $innerType) {
				yield from self::getIdentifiers($innerType);
			}

		} elseif ($typeNode instanceof GenericTypeNode) {
			yield from self::getIdentifiers($typeNode->type);
			foreach ($typeNode->genericTypes as $innerType) {
				yield from self::getIdentifiers($innerType);
			}

		} elseif ($typeNode instanceof CallableTypeNode) {
			yield $typeNode->identifier;
			yield from self::getIdentifiers($typeNode->returnType);

			foreach ($typeNode->parameters as $parameter) {
				yield from self::getIdentifiers($parameter->type);
			}

		} elseif ($typeNode instanceof ArrayShapeNode) {
			foreach ($typeNode->items as $item) {
				yield from self::getIdentifiers($item->valueType);
			}

		} elseif ($typeNode instanceof OffsetAccessTypeNode) {
			yield from self::getIdentifiers($typeNode->type);
			yield from self::getIdentifiers($typeNode->offset);

		} elseif ($typeNode instanceof ConditionalTypeNode) {
			yield from self::getIdentifiers($typeNode->subjectType);
			yield from self::getIdentifiers($typeNode->targetType);
			yield from self::getIdentifiers($typeNode->if);
			yield from self::getIdentifiers($typeNode->else);

		} elseif ($typeNode instanceof ConditionalTypeForParameterNode) {
			yield from self::getIdentifiers($typeNode->targetType);
			yield from self::getIdentifiers($typeNode->if);
			yield from self::getIdentifiers($typeNode->else);
		}
	}


	protected function resolvePhpDoc(PhpDocNode $phpDoc): void
	{
		foreach (self::getTypes($phpDoc) as $type) {
			foreach (self::getIdentifiers($type) as $identifier) {
				$lower = strtolower($identifier->name);

				if (isset(self::KEYWORDS[$identifier->name]) || isset(self::NATIVE_KEYWORDS[$lower]) || str_contains($lower, '-')) {
					$identifier->setAttribute('kind', IdentifierKind::Keyword);
					continue;
				}

				for ($i = count($this->genericNameContextStack) - 1; $i >= 0; $i--) {
					if (isset($this->genericNameContextStack[$i][$lower])) {
						$identifier->setAttribute('kind', IdentifierKind::Generic);
						continue 2;
					}
				}

				$classLikeReference = new ClassLikeReferenceInfo($this->resolveIdentifier($identifier->name));
				$identifier->setAttribute('kind', IdentifierKind::ClassLike);
				$identifier->setAttribute('classLikeReference', $classLikeReference);
			}
		}

		foreach (self::getExpressions($phpDoc) as $expr) {
			if ($expr instanceof ConstFetchNode && $expr->className !== '') {
				$expr->className = $this->resolveIdentifier($expr->className);
			}
		}
	}


	protected function resolveIdentifier(string $identifier): string
	{
		if ($identifier[0] === '\\') {
			return substr($identifier, 1);

		} else {
			return $this->nameContext->getResolvedClassName(new Node\Name($identifier))->toString();
		}
	}
}
