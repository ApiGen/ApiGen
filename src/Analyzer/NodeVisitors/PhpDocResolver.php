<?php declare(strict_types = 1);

namespace ApiGen\Analyzer\NodeVisitors;

use ApiGen\Analyzer\IdentifierKind;
use ApiGen\Analyzer\NameContextFrame;
use ApiGen\Info\AliasInfo;
use ApiGen\Info\AliasReferenceInfo;
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
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasImportTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasTagValueNode;
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

use function get_class;
use function get_debug_type;
use function sprintf;
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

			if ($node instanceof Node\Stmt\ClassLike || $node instanceof Node\FunctionLike) {
				$this->nameContextFrame = $this->resolveNameContext($phpDoc, $this->nameContextFrame, $doc->getStartLine(), $doc->getEndLine());

				if ($node instanceof Node\Stmt\ClassLike && $node->namespacedName !== null) {
					$this->nameContextFrame->scope = new ClassLikeReferenceInfo($node->namespacedName->toString());
				}

				$phpDoc->setAttribute('nameContext', $this->nameContextFrame);
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


	protected function resolveNameContext(PhpDocNode $doc, NameContextFrame $parent, ?int $startLine, ?int $endLine): NameContextFrame
	{
		$frame = new NameContextFrame($parent);

		foreach ($doc->children as $child) {
			if ($child instanceof PhpDocTagNode) {
				if ($child->value instanceof TypeAliasTagValueNode) {
					$lower = strtolower($child->value->alias);
					$frame->names[$lower] = new AliasInfo($child->value->alias, $child->value->type);
					$frame->names[$lower]->startLine = $startLine;
					$frame->names[$lower]->endLine = $endLine;

				} elseif ($child->value instanceof TypeAliasImportTagValueNode) {
					$classLike = new ClassLikeReferenceInfo($this->resolveIdentifier($child->value->importedFrom->name));
					$alias = $child->value->importedAs ?? $child->value->importedAlias;
					$lower = strtolower($alias);
					$frame->names[$lower] = new AliasReferenceInfo($classLike, $child->value->importedAlias);

				} elseif ($child->value instanceof TemplateTagValueNode) {
					$lower = strtolower($child->value->name);
					$variance = str_ends_with($child->name, '-covariant') ? GenericParameterVariance::Covariant : GenericParameterVariance::Invariant;
					$frame->names[$lower] = new GenericParameterInfo($child->value->name, $variance, $child->value->bound, $child->value->description);
				}
			}
		}

		return $frame;
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
				case TypeAliasTagValueNode::class:
					yield $tag->value->type;
					break;

				case TypeAliasImportTagValueNode::class:
					yield $tag->value->importedFrom;
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

		foreach ($phpDocNode->getAttribute('nameContext') ?? [] as $nameDef) {
			if ($nameDef instanceof GenericParameterInfo) {
				if ($nameDef->bound !== null) {
					yield $nameDef->bound;
				}

			} elseif ($nameDef instanceof AliasInfo) {
				yield $nameDef->type;
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

				$nameDef = $this->nameContextFrame->names[$lower] ?? null;

				if ($nameDef === null) {
					$classLikeReference = new ClassLikeReferenceInfo($this->resolveIdentifier($identifier->name));
					$identifier->setAttribute('kind', IdentifierKind::ClassLike);
					$identifier->setAttribute('classLikeReference', $classLikeReference);

				} elseif ($nameDef instanceof GenericParameterInfo) {
					$identifier->setAttribute('kind', IdentifierKind::Generic);

				} elseif ($nameDef instanceof AliasInfo) {
					if ($this->nameContextFrame->scope !== null) {
						$scope = $this->nameContextFrame->scope;
						$identifier->setAttribute('kind', IdentifierKind::Alias);
						$identifier->setAttribute('aliasReference', new AliasReferenceInfo($scope, $nameDef->name));

					} else {
						throw new \LogicException(sprintf('Unexpected alias %s in global scope for type %s', $nameDef->name, $nameDef->type));
					}

				} elseif ($nameDef instanceof AliasReferenceInfo) {
					$identifier->setAttribute('kind', IdentifierKind::Alias);
					$identifier->setAttribute('aliasReference', $nameDef);

				} else {
					throw new \LogicException(sprintf('Unexpected name definition %s', get_debug_type($nameDef)));
				}
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
