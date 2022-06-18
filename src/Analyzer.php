<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Analyzer\AnalyzeResult;
use ApiGenX\Analyzer\AnalyzeTask;
use ApiGenX\Analyzer\NodeVisitors\PhpDocResolver;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ClassLikeReferenceInfo;
use ApiGenX\Info\ConstantInfo;
use ApiGenX\Info\EnumCaseInfo;
use ApiGenX\Info\EnumInfo;
use ApiGenX\Info\ErrorInfo;
use ApiGenX\Info\Expr\ArgExprInfo;
use ApiGenX\Info\Expr\ArrayExprInfo;
use ApiGenX\Info\Expr\ArrayItemExprInfo;
use ApiGenX\Info\Expr\ArrayKeyFetchExprInfo;
use ApiGenX\Info\Expr\BinaryOpExprInfo;
use ApiGenX\Info\Expr\BooleanExprInfo;
use ApiGenX\Info\Expr\ClassConstantFetchExprInfo;
use ApiGenX\Info\Expr\ConstantFetchExprInfo;
use ApiGenX\Info\Expr\FloatExprInfo;
use ApiGenX\Info\Expr\IntegerExprInfo;
use ApiGenX\Info\Expr\NewExprInfo;
use ApiGenX\Info\Expr\NullExprInfo;
use ApiGenX\Info\Expr\StringExprInfo;
use ApiGenX\Info\Expr\TernaryExprInfo;
use ApiGenX\Info\Expr\UnaryOpExprInfo;
use ApiGenX\Info\ExprInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\MemberInfo;
use ApiGenX\Info\MethodInfo;
use ApiGenX\Info\MissingInfo;
use ApiGenX\Info\NameInfo;
use ApiGenX\Info\ParameterInfo;
use ApiGenX\Info\PropertyInfo;
use ApiGenX\Info\TraitInfo;
use BackedEnum;
use Iterator;
use Nette\Utils\FileSystem;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFalseNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNullNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprTrueNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symfony\Component\Console\Helper\ProgressBar;
use UnitEnum;

use function array_column;
use function array_map;
use function assert;
use function count;
use function get_debug_type;
use function implode;
use function is_array;
use function is_scalar;
use function is_string;
use function iterator_to_array;
use function sprintf;
use function strtolower;
use function substr;
use function trim;


final class Analyzer
{
	public function __construct(
		private Locator $locator,
		private Parser $parser,
		private NodeTraverserInterface $traverser,
	) {
	}


	/**
	 * @param string[] $files indexed by []
	 */
	public function analyze(ProgressBar $progressBar, array $files): AnalyzeResult
	{
		/** @var AnalyzeTask[] $tasks indexed by [path] */
		$tasks = [];

		/** @var ClassLikeInfo[] $found indexed by [classLikeName] */
		$found = [];

		/** @var ClassLikeInfo[] $missing indexed by [classLikeName] */
		$missing = [];

		/** @var ErrorInfo[][] $errors indexed by [errorKind][] */
		$errors = [];

		$schedule = static function (string $file, bool $primary) use (&$tasks, $progressBar): void {
			$file = Helpers::realPath($file);
			$tasks[$file] ??= new AnalyzeTask($file, $primary);
			$progressBar->setMaxSteps(count($tasks));
		};

		foreach ($files as $file) {
			$schedule($file, primary: true);
		}

		foreach ($tasks as &$task) {
			foreach ($this->processTask($task) as $info) {
				if ($info instanceof ClassLikeInfo) {
					foreach ($info->dependencies as $dependency) {
						if (!isset($found[$dependency->fullLower]) && !isset($missing[$dependency->fullLower])) {
							$missing[$dependency->fullLower] = $info;
							$file = $this->locator->locate($dependency);

							if ($file !== null) {
								$schedule($file, primary: false);
							}
						}
					}

					unset($missing[$info->name->fullLower]);
					$found[$info->name->fullLower] = $info;

				} elseif ($info instanceof ErrorInfo) {
					$errors[$info->kind][] = $info;

				} else {
					throw new \LogicException(sprintf('Unexpected task result %s, expected either %s or %s', get_debug_type($info), ClassLikeInfo::class, ErrorInfo::class));
				}
			}

			$progressBar->setMessage($task->sourceFile);
			$progressBar->advance();
		}

		foreach ($missing as $fullLower => $referencedBy) {
			$dependency = $referencedBy->dependencies[$fullLower];
			$errors[ErrorInfo::KIND_MISSING_SYMBOL][] = new ErrorInfo(ErrorInfo::KIND_MISSING_SYMBOL, "Missing {$dependency->full}\nreferences by {$referencedBy->name->full}");
			$found[$dependency->fullLower] = new MissingInfo(new NameInfo($dependency->full, $dependency->fullLower), $referencedBy->name);
		}

		return new AnalyzeResult($found, $errors);
	}


	/**
	 * @return ClassLikeInfo[]|ErrorInfo[]
	 */
	private function processTask(AnalyzeTask $task): array
	{
		try {
			$ast = $this->parser->parse(FileSystem::read($task->sourceFile)) ?? throw new \LogicException();
			$ast = $this->traverser->traverse($ast);
			return iterator_to_array($this->processNodes($task, $ast), preserve_keys: false);

		} catch (\PhpParser\Error $e) {
			$error = new ErrorInfo(ErrorInfo::KIND_SYNTAX_ERROR, "Parse error in file {$task->sourceFile}:\n{$e->getMessage()}");
			return [$error];

		} catch (\Throwable $e) {
			throw new \LogicException("Failed to analyze file $task->sourceFile", 0, $e);
		}
	}


	/**
	 * @param  Node[] $nodes indexed by []
	 * @return Iterator<ClassLikeInfo>
	 */
	private function processNodes(AnalyzeTask $task, array $nodes): Iterator // TODO: move to astTraverser?
	{
		foreach ($nodes as $node) {
			if ($node instanceof Node\Stmt\Namespace_) {
				yield from $this->processNodes($task, $node->stmts);

			} elseif ($node instanceof Node\Stmt\ClassLike && $node->name !== null) {
				try {
					yield $this->processClassLike($task, $node); // TODO: functions, constants, class aliases

				} catch (\Throwable $e) {
					throw new \LogicException("Failed to analyze $node->name", 0, $e);
				}

			} elseif ($node instanceof Node) {
				foreach ($node->getSubNodeNames() as $name) {
					$subNode = $node->$name;

					if (is_array($subNode)) {
						yield from $this->processNodes($task, $subNode);

					} elseif ($subNode instanceof Node) {
						yield from $this->processNodes($task, [$subNode]);
					}
				}
			}
		}
	}


	private function processClassLike(AnalyzeTask $task, Node\Stmt\ClassLike $node): ClassLikeInfo // TODO: handle trait usage
	{
		assert($node->namespacedName !== null);
		$name = new NameInfo($node->namespacedName->toString());

		if ($node instanceof Node\Stmt\Class_) {
			$info = new ClassInfo($name, $task->primary);
			$info->abstract = $node->isAbstract();
			$info->final = $node->isFinal();
			$info->extends = $node->extends ? $this->processName($node->extends) : null;
			$info->implements = $this->processNameList($node->implements);

			foreach ($node->getTraitUses() as $traitUse) {
				$info->uses += $this->processNameList($traitUse->traits);
			}

			$info->dependencies += $info->extends ? [$info->extends->fullLower => $info->extends] : [];
			$info->dependencies += $info->implements;
			$info->dependencies += $info->uses;

		} elseif ($node instanceof Node\Stmt\Interface_) {
			$info = new InterfaceInfo($name, $task->primary);
			$info->extends = $this->processNameList($node->extends);
			$info->dependencies += $info->extends;

		} elseif ($node instanceof Node\Stmt\Trait_) {
			$info = new TraitInfo($name, $task->primary);

		} elseif ($node instanceof Node\Stmt\Enum_) {
			$autoImplement = new ClassLikeReferenceInfo($node->scalarType ? BackedEnum::class : UnitEnum::class);

			$info = new EnumInfo($name, $task->primary);
			$info->scalarType = $node->scalarType?->name;
			$info->implements = $this->processNameList($node->implements) + [$autoImplement->fullLower => $autoImplement];

			foreach ($node->getTraitUses() as $traitUse) {
				$info->uses += $this->processNameList($traitUse->traits);
			}

			$info->dependencies += $info->implements;
			$info->dependencies += $info->uses;

		} else {
			throw new \LogicException(sprintf('Unsupported ClassLike node %s', get_debug_type($node)));
		}

		$classDoc = $this->extractPhpDoc($node);
		$info->description = $this->extractDescription($classDoc);
		$info->tags = $this->extractTags($classDoc);
		$info->file = $task->sourceFile;
		$info->startLine = $node->getStartLine();
		$info->endLine = $node->getEndLine();

		foreach ($this->extractMembers($info->tags, $node) as $member) {
			if ($member instanceof ConstantInfo) {
				$info->constants[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->value);

			} elseif ($member instanceof PropertyInfo) {
				$info->properties[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->default);
				$info->dependencies += $this->extractTypeDependencies($member->type);

			} elseif ($member instanceof MethodInfo) {
				$info->methods[$member->nameLower] = $member;
				$info->dependencies += $this->extractTypeDependencies($member->returnType);

				foreach ($member->parameters as $parameterInfo) {
					$info->dependencies += $this->extractTypeDependencies($parameterInfo->type);
					$info->dependencies += $this->extractExprDependencies($parameterInfo->default);
				}

			} elseif ($member instanceof EnumCaseInfo) {
				assert($info instanceof EnumInfo);
				$info->cases[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->value);

			} else {
				throw new \LogicException(sprintf('Unexpected member type %s', get_debug_type($member)));
			}
		}

		return $info;
	}


	/**
	 * @param  PhpDocTagValueNode[][] $tags indexed by [tagName][]
	 * @return iterable<MemberInfo>
	 */
	private function extractMembers(array $tags, Node\Stmt\ClassLike $node): iterable
	{
		yield from $this->extractMembersClassLikeBody($node);
		yield from $this->extractMembersClassLikeTags($tags);
	}


	/**
	 * @return iterable<MemberInfo>
	 */
	private function extractMembersClassLikeBody(Node\Stmt\ClassLike $node): iterable
	{
		foreach ($node->stmts as $member) {
			$memberDoc = $this->extractPhpDoc($member);
			$description = $this->extractDescription($memberDoc);
			$tags = $this->extractTags($memberDoc);

			if ($member instanceof Node\Stmt\ClassConst) {
				foreach ($member->consts as $constant) {
					$memberInfo = new ConstantInfo($constant->name->name, $this->processExpr($constant->value));

					$memberInfo->description = $description;
					$memberInfo->tags = $tags;

					$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
					$memberInfo->endLine = $member->getEndLine();

					$memberInfo->public = $member->isPublic();
					$memberInfo->protected = $member->isProtected();
					$memberInfo->private = $member->isPrivate();
					$memberInfo->final = $member->isFinal();

					yield $memberInfo;
				}

			} elseif ($member instanceof Node\Stmt\Property) {
				$varTag = isset($tags['var'][0]) && $tags['var'][0] instanceof VarTagValueNode ? $tags['var'][0] : null;

				foreach ($member->props as $property) {
					$memberInfo = new PropertyInfo($property->name->name);

					$memberInfo->description = $varTag ? $varTag->description : $description;
					$memberInfo->tags = $tags;

					$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
					$memberInfo->endLine = $member->getEndLine();

					$memberInfo->public = $member->isPublic();
					$memberInfo->protected = $member->isProtected();
					$memberInfo->private = $member->isPrivate();
					$memberInfo->static = $member->isStatic();
					$memberInfo->readOnly = $member->isReadonly();

					$memberInfo->type = $varTag ? $varTag->type : $this->processTypeOrNull($member->type);
					$memberInfo->default = $this->processExprOrNull($property->default);

					yield $memberInfo;
				}

			} elseif ($member instanceof Node\Stmt\ClassMethod) {
				$returnTag = isset($tags['return'][0]) && $tags['return'][0] instanceof ReturnTagValueNode ? $tags['return'][0] : null;

				$memberInfo = new MethodInfo($member->name->name);

				$memberInfo->description = $description;
				$memberInfo->tags = $tags;

				$memberInfo->parameters = $this->processParameters($memberDoc->getParamTagValues(), $member->params);
				$memberInfo->returnType = $returnTag ? $returnTag->type : $this->processTypeOrNull($member->returnType);
				$memberInfo->byRef = $member->byRef;

				$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
				$memberInfo->endLine = $member->getEndLine();

				$memberInfo->public = $member->isPublic();
				$memberInfo->protected = $member->isProtected();
				$memberInfo->private = $member->isPrivate();

				$memberInfo->static = $member->isStatic();
				$memberInfo->abstract = $member->isAbstract();
				$memberInfo->final = $member->isFinal();

				yield $memberInfo;

				if ($member->name->toLowerString() === '__construct') {
					foreach ($member->params as $param) {
						if ($param->flags === 0) {
							continue;
						}

						assert($param->var instanceof Node\Expr\Variable);
						assert(is_string($param->var->name));
						$propertyInfo = new PropertyInfo($param->var->name);

						$propertyInfo->description = $memberInfo->parameters[$propertyInfo->name]->description;

						$propertyInfo->startLine = $param->getStartLine();
						$propertyInfo->endLine = $param->getEndLine();

						$propertyInfo->public = (bool) ($param->flags & Node\Stmt\Class_::MODIFIER_PUBLIC);
						$propertyInfo->protected = (bool) ($param->flags & Node\Stmt\Class_::MODIFIER_PROTECTED);
						$propertyInfo->private = (bool) ($param->flags & Node\Stmt\Class_::MODIFIER_PRIVATE);

						$propertyInfo->type = $memberInfo->parameters[$propertyInfo->name]->type;

						yield $propertyInfo;
					}
				}

			} elseif ($member instanceof Node\Stmt\EnumCase) {
				$memberInfo = new EnumCaseInfo($member->name->name, $this->processExprOrNull($member->expr));

				$memberInfo->description = $description;
				$memberInfo->tags = $tags;

				$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
				$memberInfo->endLine = $member->getEndLine();

				yield $memberInfo;
			}
		}
	}


	/**
	 * @param  PhpDocTagValueNode[][] $tags indexed by [tagName][]
	 * @return iterable<MemberInfo>
	 */
	private function extractMembersClassLikeTags(array $tags): iterable
	{
		$propertyTags = [
			'property' => [false, false],
			'property-read' => [true, false],
			'property-write' => [false, true],
		];

		foreach ($propertyTags as $tag => [$readOnly, $writeOnly]) {
			/** @var PropertyTagValueNode $value */
			foreach ($tags[$tag] ?? [] as $value) {
				$propertyInfo = new PropertyInfo(substr($value->propertyName, 1));
				$propertyInfo->magic = true;
				$propertyInfo->public = true;
				$propertyInfo->type = $value->type;
				$propertyInfo->description = $value->description;
				$propertyInfo->readOnly = $readOnly;
				$propertyInfo->writeOnly = $writeOnly;

				yield $propertyInfo;
			}
		}

		/** @var MethodTagValueNode $value */
		foreach ($tags['method'] ?? [] as $value) {
			$methodInfo = new MethodInfo($value->methodName);
			$methodInfo->magic = true;
			$methodInfo->public = true;
			$methodInfo->static = $value->isStatic;
			$methodInfo->returnType = $value->returnType;
			$methodInfo->description = $value->description;

			foreach ($value->parameters as $parameter) {
				$parameterInfo = new ParameterInfo(substr($parameter->parameterName, 1));
				$parameterInfo->type = $parameter->type;
				$parameterInfo->byRef = $parameter->isReference;
				$parameterInfo->variadic = $parameter->isVariadic;
				$parameterInfo->default = $parameter->defaultValue ? $this->processPhpStanExpr($parameter->defaultValue) : null;

				$methodInfo->parameters[$parameterInfo->name] = $parameterInfo;
			}

			yield $methodInfo;
		}
	}


	/**
	 * @param  ParamTagValueNode[] $paramTags
	 * @param  Node\Param[]	       $parameters
	 * @return ParameterInfo[]
	 */
	private function processParameters(array $paramTags, array $parameters): array
	{
		$paramTags = array_column($paramTags, null, 'parameterName');

		$parameterInfos = [];
		foreach ($parameters as $parameter) {
			assert($parameter->var instanceof Node\Expr\Variable);
			assert(is_scalar($parameter->var->name));

			$paramTag = $paramTags["\${$parameter->var->name}"] ?? null;
			$parameterInfo = new ParameterInfo($parameter->var->name);
			$parameterInfo->description = $paramTag ? $paramTag->description : '';
			$parameterInfo->type = $paramTag ? $paramTag->type : $this->processTypeOrNull($parameter->type);
			$parameterInfo->byRef = $parameter->byRef;
			$parameterInfo->variadic = $parameter->variadic || ($paramTag && $paramTag->isVariadic);
			$parameterInfo->default = $this->processExprOrNull($parameter->default);

			$parameterInfos[$parameter->var->name] = $parameterInfo;
		}

		return $parameterInfos;
	}


	private function processName(Node\Name $name): ClassLikeReferenceInfo
	{
		return new ClassLikeReferenceInfo($name->toString());
	}


	/**
	 * @param  Node\Name[] $names indexed by []
	 * @return ClassLikeReferenceInfo[] indexed by [classLikeName]
	 */
	private function processNameList(array $names): array
	{
		$nameMap = [];

		foreach ($names as $name) {
			$nameInfo = $this->processName($name);
			$nameMap[$nameInfo->fullLower] = $nameInfo;
		}

		return $nameMap;
	}


	private function processTypeOrNull(Identifier|Name|ComplexType|null $node): ?TypeNode
	{
		return $node ? $this->processType($node) : null;
	}


	private function processType(Identifier|Name|ComplexType $node): TypeNode
	{
		if ($node instanceof ComplexType) {
			if ($node instanceof NullableType) {
				return new NullableTypeNode($this->processType($node->type));
			}

			if ($node instanceof UnionType) {
				return new UnionTypeNode(array_map([$this, 'processType'], $node->types));
			}

			if ($node instanceof IntersectionType) {
				return new IntersectionTypeNode(array_map([$this, 'processType'], $node->types));
			}

			throw new \LogicException(sprintf('Unsupported complex type %s', get_debug_type($node)));
		}

		return new IdentifierTypeNode($node->toString());
	}


	private function processExprOrNull(?Node\Expr $expr): ?ExprInfo
	{
		return $expr ? $this->processExpr($expr) : null;
	}


	private function processExpr(Node\Expr $expr): ExprInfo
	{
		if ($expr instanceof Node\Scalar\LNumber) {
			return new IntegerExprInfo($expr->value, $expr->getAttribute('kind'), $expr->getAttribute('rawValue'));

		} elseif ($expr instanceof Node\Scalar\DNumber) {
			return new FloatExprInfo($expr->value, $expr->getAttribute('rawValue'));

		} elseif ($expr instanceof Node\Scalar\String_) {
			return new StringExprInfo($expr->value, $expr->getAttribute('rawValue'));

		} elseif ($expr instanceof Node\Expr\Array_) {
			$items = [];

			foreach ($expr->items as $item) {
				assert($item !== null);
				$key = $this->processExprOrNull($item->key);
				$value = $this->processExpr($item->value);
				$items[] = new ArrayItemExprInfo($key, $value);
			}

			return new ArrayExprInfo($items);

		} elseif ($expr instanceof Node\Expr\ClassConstFetch) {
			assert($expr->class instanceof Node\Name);
			assert($expr->name instanceof Node\Identifier);

			// TODO: handle 'self' & 'parent' differently?
			return new ClassConstantFetchExprInfo($this->processName($expr->class), $expr->name->toString());

		} elseif ($expr instanceof Node\Expr\ConstFetch) {
			$lower = $expr->name->toLowerString();

			if ($lower === 'true') {
				return new BooleanExprInfo(true);

			} elseif ($lower === 'false') {
				return new BooleanExprInfo(false);

			} elseif ($lower === 'null') {
				return new NullExprInfo();

			} else {
				return new ConstantFetchExprInfo($expr->name->toString());
			}

		} elseif ($expr instanceof Node\Scalar\MagicConst) {
			return new ConstantFetchExprInfo($expr->getName());

		} elseif ($expr instanceof Node\Expr\UnaryMinus) {
			return new UnaryOpExprInfo('-', $this->processExpr($expr->expr));

		} elseif ($expr instanceof Node\Expr\UnaryPlus) {
			return new UnaryOpExprInfo('+', $this->processExpr($expr->expr));

		} elseif ($expr instanceof Node\Expr\BinaryOp) {
			return new BinaryOpExprInfo(
				$expr->getOperatorSigil(),
				$this->processExpr($expr->left),
				$this->processExpr($expr->right),
			);

		} elseif ($expr instanceof Node\Expr\Ternary) {
			return new TernaryExprInfo(
				$this->processExpr($expr->cond),
				$this->processExprOrNull($expr->if),
				$this->processExpr($expr->else),
			);

		} elseif ($expr instanceof Node\Expr\ArrayDimFetch) {
			assert($expr->dim !== null);
			return new ArrayKeyFetchExprInfo(
				$this->processExpr($expr->var),
				$this->processExpr($expr->dim),
			);

		} elseif ($expr instanceof Node\Expr\New_) {
			assert($expr->class instanceof Name);

			$args = [];
			foreach ($expr->args as $arg) {
				assert($arg instanceof Node\Arg);
				$args[] = new ArgExprInfo($arg->name?->name, $this->processExpr($arg->value));
			}

			return new NewExprInfo($this->processName($expr->class), $args);

		} else {
			throw new \LogicException(sprintf('Unsupported expr node %s used in constant expression', get_debug_type($expr)));
		}
	}


	private function processPhpStanExpr(ConstExprNode $expr): ExprInfo
	{
		if ($expr instanceof ConstExprTrueNode) {
			return new BooleanExprInfo(true);

		} elseif ($expr instanceof ConstExprFalseNode) {
			return new BooleanExprInfo(false);

		} elseif ($expr instanceof ConstExprNullNode) {
			return new NullExprInfo();

		} elseif ($expr instanceof ConstExprIntegerNode) {
			return $this->processExpr(Node\Scalar\LNumber::fromString($expr->value));

		} elseif ($expr instanceof ConstExprFloatNode) {
			return new FloatExprInfo(Node\Scalar\DNumber::parse($expr->value), $expr->value);

		} elseif ($expr instanceof ConstExprStringNode) {
			return new StringExprInfo(Node\Scalar\String_::parse($expr->value), $expr->value);

		} elseif ($expr instanceof ConstExprArrayNode) {
			$items = [];

			foreach ($expr->items as $item) {
				$items[] = new ArrayItemExprInfo(
					$item->key ? $this->processPhpStanExpr($item->key) : null,
					$this->processPhpStanExpr($item->value),
				);
			}

			return new ArrayExprInfo($items);

		} elseif ($expr instanceof ConstFetchNode) {
			if ($expr->className === '') {
				return new ConstantFetchExprInfo($expr->name);

			} else {
				return new ClassConstantFetchExprInfo(new ClassLikeReferenceInfo($expr->className), $expr->name);
			}

		} else {
			throw new \LogicException(sprintf('Unsupported const expr node %s used in PhpDoc', get_debug_type($expr)));
		}
	}


	private function extractPhpDoc(Node $node): PhpDocNode
	{
		return $node->getAttribute('phpDoc') ?? new PhpDocNode([]);
	}


	private function extractDescription(PhpDocNode $node): string
	{
		$lines = [];
		foreach ($node->children as $child) {
			if ($child instanceof PhpDocTextNode) {
				$lines[] = $child->text;

			} else {
				break;
			}
		}

		return trim(implode("\n", $lines));
	}


	/**
	 * @return PhpDocTagValueNode[][] indexed by [tagName][]
	 */
	private function extractTags(PhpDocNode $node): array
	{
		$tags = [];

		foreach ($node->getTags() as $tag) {
			if (!$tag->value instanceof InvalidTagValueNode) {
				$tags[substr($tag->name, 1)][] = $tag->value;
			}
		}

		return $tags;
	}


	/**
	 * @return ClassLikeReferenceInfo[] indexed by [classLike]
	 */
	private function extractExprDependencies(?ExprInfo $expr): array
	{
		$dependencies = [];

		if ($expr instanceof ArrayExprInfo) {
			foreach ($expr->items as $item) {
				$dependencies += $this->extractExprDependencies($item->key);
				$dependencies += $this->extractExprDependencies($item->value);
			}

		} elseif ($expr instanceof UnaryOpExprInfo) {
			$dependencies += $this->extractExprDependencies($expr->expr);

		} elseif ($expr instanceof BinaryOpExprInfo) {
			$dependencies += $this->extractExprDependencies($expr->left);
			$dependencies += $this->extractExprDependencies($expr->right);

		} elseif ($expr instanceof TernaryExprInfo) {
			$dependencies += $this->extractExprDependencies($expr->condition);
			$dependencies += $this->extractExprDependencies($expr->if);
			$dependencies += $this->extractExprDependencies($expr->else);

		} elseif ($expr instanceof ArrayKeyFetchExprInfo) {
			$dependencies += $this->extractExprDependencies($expr->array);
			$dependencies += $this->extractExprDependencies($expr->key);

		} elseif ($expr instanceof ClassConstantFetchExprInfo) {
			if ($expr->classLike->fullLower !== 'self' && $expr->classLike->fullLower !== 'parent') {
				$dependencies[$expr->classLike->fullLower] = $expr->classLike;
			}

		} elseif ($expr instanceof NewExprInfo) {
			if ($expr->classLike->fullLower !== 'self' && $expr->classLike->fullLower !== 'parent') {
				$dependencies[$expr->classLike->fullLower] = $expr->classLike;
			}

			foreach ($expr->args as $arg) {
				$dependencies += $this->extractExprDependencies($arg->value);
			}
		}

		return $dependencies;
	}


	/**
	 * @return ClassLikeReferenceInfo[] indexed by [classLike]
	 */
	private function extractTypeDependencies(?TypeNode $type): array
	{
		$dependencies = [];

		if ($type !== null) {
			foreach (PhpDocResolver::getIdentifiers($type) as $identifier) {
				if ($identifier->getAttribute('kind') === 'classLike') {
					$lower = strtolower($identifier->name);
					$dependencies[$lower] = new ClassLikeReferenceInfo($identifier->name, $lower);
				}
			}
		}

		return $dependencies;
	}
}
