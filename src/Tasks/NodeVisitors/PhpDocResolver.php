<?php declare(strict_types = 1);

namespace ApiGenX\Tasks\NodeVisitors;

use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;


final class PhpDocResolver extends NodeVisitorAbstract
{
	public const KEYWORDS = [
		'int' => true, 'integer' => true, 'string' => true, 'bool' => true, 'boolean' => true, 'true' => true,
		'false' => true, 'null' => true, 'float' => true, 'double' => true, 'array' => true, 'scalar' => true,
		'number' => true, 'iterable' => true, 'callable' => true, 'resource' => true, 'mixed' => true,
		'void' => true, 'object' => true, 'never' => true, 'self' => true, 'static' => true, 'parent' => true,
	];

	/** @var Lexer */
	private $lexer;

	/** @var PhpDocParser */
	private $parser;

	/** @var NameContext */
	private $nameContext;


	public function __construct(Lexer $lexer, PhpDocParser $parser, NameContext $nameContext)
	{
		$this->lexer = $lexer;
		$this->parser = $parser;
		$this->nameContext = $nameContext;
	}


	public function enterNode(Node $node)
	{
		$doc = $node->getDocComment();

		if ($doc === null) {
			return null;
		}

		$tokens = $this->lexer->tokenize($doc->getText());
		$phpDoc = $this->parser->parse(new TokenIterator($tokens));


		$this->resolvePhpDoc($phpDoc);
		$node->setAttribute('phpDoc', $phpDoc);
	}


	/**
	 * @return iterable|TypeNode[]
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
	}


	/**
	 * @return iterable|IdentifierTypeNode[]
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
		}
	}


	private function resolvePhpDoc(PhpDocNode $phpDoc): void
	{
		foreach (self::getTypes($phpDoc) as $type) {
			foreach (self::getIdentifiers($type) as $identifier) {
				if (!isset(self::KEYWORDS[strtolower($identifier->name)])) {
					if ($identifier->name[0] === '\\') {
						$identifier->name = substr($identifier->name, 1);
					} else {
						$identifier->name = $this->nameContext->getResolvedClassName(new Node\Name($identifier->name))->toString();
					}
				}
			}
		}
	}
}
