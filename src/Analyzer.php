<?php declare(strict_types = 1);

namespace ApiGen;

use ApiGen\Analyzer\AnalyzeResult;
use ApiGen\Analyzer\AnalyzeTask;
use ApiGen\Analyzer\Filter;
use ApiGen\Analyzer\IdentifierKind;
use ApiGen\Analyzer\NodeVisitors\PhpDocResolver;
use ApiGen\Info\ClassInfo;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Info\ClassLikeReferenceInfo;
use ApiGen\Info\ConstantInfo;
use ApiGen\Info\EnumCaseInfo;
use ApiGen\Info\EnumInfo;
use ApiGen\Info\ErrorInfo;
use ApiGen\Info\Expr\ArgExprInfo;
use ApiGen\Info\Expr\ArrayExprInfo;
use ApiGen\Info\Expr\ArrayItemExprInfo;
use ApiGen\Info\Expr\BinaryOpExprInfo;
use ApiGen\Info\Expr\BooleanExprInfo;
use ApiGen\Info\Expr\ClassConstantFetchExprInfo;
use ApiGen\Info\Expr\ConstantFetchExprInfo;
use ApiGen\Info\Expr\DimFetchExprInfo;
use ApiGen\Info\Expr\FloatExprInfo;
use ApiGen\Info\Expr\IntegerExprInfo;
use ApiGen\Info\Expr\NewExprInfo;
use ApiGen\Info\Expr\NullExprInfo;
use ApiGen\Info\Expr\NullSafePropertyFetchExprInfo;
use ApiGen\Info\Expr\PropertyFetchExprInfo;
use ApiGen\Info\Expr\StringExprInfo;
use ApiGen\Info\Expr\TernaryExprInfo;
use ApiGen\Info\Expr\UnaryOpExprInfo;
use ApiGen\Info\ExprInfo;
use ApiGen\Info\FunctionInfo;
use ApiGen\Info\InterfaceInfo;
use ApiGen\Info\MemberInfo;
use ApiGen\Info\MethodInfo;
use ApiGen\Info\MissingInfo;
use ApiGen\Info\NameInfo;
use ApiGen\Info\ParameterInfo;
use ApiGen\Info\PropertyInfo;
use ApiGen\Info\TraitInfo;
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
use PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ImplementsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MixinTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\UsesTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symfony\Component\Console\Helper\ProgressBar;
use UnitEnum;

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
use function substr;
use function trim;


class Analyzer
{
	public function __construct(
		protected Locator $locator,
		protected Parser $parser,
		protected NodeTraverserInterface $traverser,
		protected Filter $filter,
	) {
	}


	/**
	 * @param string[] $files indexed by []
	 */
	public function analyze(ProgressBar $progressBar, array $files): AnalyzeResult
	{
		/** @var AnalyzeTask[] $tasks indexed by [path] */
		$tasks = [];

		/** @var ClassLikeInfo[] $classLike indexed by [classLikeName] */
		$classLike = [];

		/** @var ClassLikeInfo[] $missing indexed by [classLikeName] */
		$missing = [];

		/** @var FunctionInfo[] $functions indexed by [functionName] */
		$functions = [];

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
				if ($info instanceof ClassLikeInfo || $info instanceof FunctionInfo) {
					foreach ($info->dependencies as $dependency) {
						if (!isset($classLike[$dependency->fullLower]) && !isset($missing[$dependency->fullLower])) {
							$missing[$dependency->fullLower] = $info;
							$file = $this->locator->locate($dependency);

							if ($file !== null) {
								$schedule($file, primary: false);
							}
						}
					}
				}

				if ($info instanceof ClassLikeInfo) {
					if (isset($classLike[$info->name->fullLower])) {
						$errors[ErrorInfo::KIND_DUPLICATE_SYMBOL][] = $this->createDuplicateSymbolError($info, $classLike[$info->name->fullLower]);

					} else {
						unset($missing[$info->name->fullLower]);
						$classLike[$info->name->fullLower] = $info;
					}

				} elseif ($info instanceof FunctionInfo) {
					if (isset($functions[$info->name->fullLower])) {
						$errors[ErrorInfo::KIND_DUPLICATE_SYMBOL][] = $this->createDuplicateSymbolError($info, $functions[$info->name->fullLower]);

					} else {
						$functions[$info->name->fullLower] = $info;
					}

				} elseif ($info instanceof ErrorInfo) {
					$errors[$info->kind][] = $info;

				} else {
					throw new \LogicException(sprintf('Unexpected task result %s', get_debug_type($info)));
				}
			}

			$progressBar->setMessage($task->sourceFile);
			$progressBar->advance();
		}

		foreach ($missing as $fullLower => $referencedBy) {
			$dependency = $referencedBy->dependencies[$fullLower];
			$errors[ErrorInfo::KIND_MISSING_SYMBOL][] = new ErrorInfo(ErrorInfo::KIND_MISSING_SYMBOL, "Missing {$dependency->full}\nreferenced by {$referencedBy->name->full}");
			$classLike[$dependency->fullLower] = new MissingInfo(new NameInfo($dependency->full, $dependency->fullLower), $referencedBy->name);
		}

		return new AnalyzeResult($classLike, $functions, $errors);
	}


	/**
	 * @return array<ClassLikeInfo | FunctionInfo | ErrorInfo>
	 */
	protected function processTask(AnalyzeTask $task): array
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
	 * @return Iterator<ClassLikeInfo | FunctionInfo>
	 */
	protected function processNodes(AnalyzeTask $task, array $nodes): Iterator
	{
		foreach ($nodes as $node) {
			if ($node instanceof Node\Stmt\Namespace_) {
				yield from $this->processNodes($task, $node->stmts);

			} elseif ($node instanceof Node\Stmt\ClassLike && $node->name !== null) {
				try {
					$task->primary = $task->primary && $this->filter->filterClassLikeNode($node);
					yield $this->processClassLike($task, $node);

				} catch (\Throwable $e) {
					throw new \LogicException("Failed to analyze $node->namespacedName", 0, $e);
				}

			} elseif ($node instanceof Node\Stmt\Function_) {
				try {
					$functionInfo = $this->processFunction($task, $node);
					yield from $functionInfo ? [$functionInfo] : [];

				} catch (\Throwable $e) {
					throw new \LogicException("Failed to analyze $node->namespacedName", 0, $e);
				}

			} elseif ($node instanceof Node\Stmt) { // TODO: constants, class aliases
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


	protected function processClassLike(AnalyzeTask $task, Node\Stmt\ClassLike $node): ClassLikeInfo
	{
		$extendsTagNames = ['extends', 'template-extends', 'phpstan-extends'];
		$implementsTagNames = ['implements', 'template-implements', 'phpstan-implements'];
		$useTagNames = ['use', 'template-use', 'phpstan-use'];

		assert($node->namespacedName !== null);
		$name = new NameInfo($node->namespacedName->toString());

		$classDoc = $this->extractPhpDoc($node);
		$tags = $this->extractTags($classDoc);

		if ($task->primary && !$this->filter->filterClassLikeTags($tags)) {
			$task->primary = false;
		}

		if ($node instanceof Node\Stmt\Class_) {
			$info = new ClassInfo($name, $task->primary);
			$info->abstract = $node->isAbstract();
			$info->final = $node->isFinal();
			$info->readOnly = $node->isReadonly();
			$info->extends = $node->extends ? $this->processName($node->extends, $tags, $extendsTagNames) : null;
			$info->implements = $this->processNameList($node->implements, $tags, $implementsTagNames);

			foreach ($node->getTraitUses() as $traitUse) { // TODO: trait adaptations
				$info->uses += $this->processNameList($traitUse->traits, $tags, $useTagNames);
			}

			$info->dependencies += $info->extends ? [$info->extends->fullLower => $info->extends] : [];
			$info->dependencies += $info->implements;
			$info->dependencies += $info->uses;

		} elseif ($node instanceof Node\Stmt\Interface_) {
			$info = new InterfaceInfo($name, $task->primary);
			$info->extends = $this->processNameList($node->extends, $tags, $extendsTagNames);
			$info->dependencies += $info->extends;

		} elseif ($node instanceof Node\Stmt\Trait_) {
			$info = new TraitInfo($name, $task->primary);

		} elseif ($node instanceof Node\Stmt\Enum_) {
			$autoImplement = new ClassLikeReferenceInfo($node->scalarType ? BackedEnum::class : UnitEnum::class);

			$info = new EnumInfo($name, $task->primary);
			$info->scalarType = $node->scalarType?->name;
			$info->implements = $this->processNameList($node->implements, $tags, $implementsTagNames) + [$autoImplement->fullLower => $autoImplement];

			foreach ($node->getTraitUses() as $traitUse) {
				$info->uses += $this->processNameList($traitUse->traits, $tags, $useTagNames);
			}

			$info->dependencies += $info->implements;
			$info->dependencies += $info->uses;

		} else {
			throw new \LogicException(sprintf('Unsupported ClassLike node %s', get_debug_type($node)));
		}

		$info->genericParameters = $classDoc->getAttribute('genericNameContext') ?? [];
		$info->description = $this->extractDescription($classDoc);
		$info->tags = $tags;
		$info->file = $task->sourceFile;
		$info->startLine = $node->getStartLine();
		$info->endLine = $node->getEndLine();

		$info->mixins = $this->processMixinTags($tags['mixin'] ?? []);
		$info->dependencies += $info->mixins;

		foreach ($info->genericParameters as $genericParameter) {
			$info->dependencies += $this->extractTypeDependencies($genericParameter->bound);
		}

		foreach ($this->extractMembers($info->tags, $node) as $member) {
			if (!$this->filter->filterMemberInfo($info, $member)) {
				continue;

			} elseif ($member instanceof ConstantInfo) {
				$info->constants[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->value);

			} elseif ($member instanceof PropertyInfo) {
				$info->properties[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->default);
				$info->dependencies += $this->extractTypeDependencies($member->type);

			} elseif ($member instanceof MethodInfo) {
				$info->methods[$member->nameLower] = $member;
				$info->dependencies += $this->extractTypeDependencies($member->returnType);

				foreach ($member->tags['throws'] ?? [] as $tagValue) {
					assert($tagValue instanceof ThrowsTagValueNode);
					$info->dependencies += $this->extractTypeDependencies($tagValue->type);
				}

				foreach ($member->parameters as $parameterInfo) {
					$info->dependencies += $this->extractTypeDependencies($parameterInfo->type);
					$info->dependencies += $this->extractExprDependencies($parameterInfo->default);
				}

				foreach ($member->genericParameters as $genericParameter) {
					$info->dependencies += $this->extractTypeDependencies($genericParameter->bound);
				}

			} elseif ($member instanceof EnumCaseInfo) {
				assert($info instanceof EnumInfo);
				$info->cases[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->value);

			} else {
				throw new \LogicException(sprintf('Unexpected member type %s', get_debug_type($member)));
			}
		}

		foreach ($info->dependencies as $dependency) {
			foreach ($dependency->genericArgs as $genericArg) {
				$info->dependencies += $this->extractTypeDependencies($genericArg);
			}
		}

		if ($info->primary && !$this->filter->filterClassLikeInfo($info)) {
			$info->primary = false;
		}

		return $info;
	}


	/**
	 * @param  PhpDocTagValueNode[][] $tags indexed by [tagName][]
	 * @return iterable<MemberInfo>
	 */
	protected function extractMembers(array $tags, Node\Stmt\ClassLike $node): iterable
	{
		yield from $this->extractMembersFromBody($node);
		yield from $this->extractMembersFromTags($tags);
	}


	/**
	 * @return iterable<MemberInfo>
	 */
	protected function extractMembersFromBody(Node\Stmt\ClassLike $node): iterable
	{
		foreach ($node->stmts as $member) {
			$memberDoc = $this->extractPhpDoc($member);
			$description = $this->extractDescription($memberDoc);
			$tags = $this->extractTags($memberDoc);

			if (!$this->filter->filterMemberTags($tags)) {
				continue;
			}

			if ($member instanceof Node\Stmt\ClassConst) {
				if (!$this->filter->filterConstantNode($member)) {
					continue;
				}

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
				if (!$this->filter->filterPropertyNode($member)) {
					continue;
				}

				$varTag = isset($tags['var'][0]) && $tags['var'][0] instanceof VarTagValueNode ? $tags['var'][0] : null;
				unset($tags['var']);

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
				if (!$this->filter->filterMethodNode($member)) {
					continue;
				}

				/** @var ?ReturnTagValueNode $returnTag */
				$returnTag = isset($tags['return'][0]) && $tags['return'][0] instanceof ReturnTagValueNode ? $tags['return'][0] : null;
				unset($tags['param'], $tags['return']);

				$memberInfo = new MethodInfo($member->name->name);

				$memberInfo->description = $description;
				$memberInfo->tags = $tags;

				$memberInfo->genericParameters = $memberDoc->getAttribute('genericNameContext') ?? [];
				$memberInfo->parameters = $this->processParameters($this->extractParamTagValues($memberDoc), $member->params);
				$memberInfo->returnType = $returnTag ? $returnTag->type : $this->processTypeOrNull($member->returnType);
				$memberInfo->returnDescription = $returnTag?->description ?? '';
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
						if ($param->flags === 0 || !$this->filter->filterPromotedPropertyNode($param)) {
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

						$propertyInfo->readOnly = (bool) ($param->flags & Node\Stmt\Class_::MODIFIER_READONLY);
						$propertyInfo->type = $memberInfo->parameters[$propertyInfo->name]->type;

						yield $propertyInfo;
					}
				}

			} elseif ($member instanceof Node\Stmt\EnumCase) {
				if (!$this->filter->filterEnumCaseNode($member)) {
					continue;
				}

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
	protected function extractMembersFromTags(array $tags): iterable
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

			foreach ($value->parameters as $position => $parameter) {
				$parameterInfo = new ParameterInfo(substr($parameter->parameterName, 1), $position);
				$parameterInfo->type = $parameter->type;
				$parameterInfo->byRef = $parameter->isReference;
				$parameterInfo->variadic = $parameter->isVariadic;
				$parameterInfo->default = $parameter->defaultValue ? $this->processPhpDocExpr($parameter->defaultValue) : null;

				$methodInfo->parameters[$parameterInfo->name] = $parameterInfo;
			}

			yield $methodInfo;
		}
	}


	protected function processFunction(AnalyzeTask $task, Node\Stmt\Function_ $node): ?FunctionInfo
	{
		if (!$this->filter->filterFunctionNode($node)) {
			return null;
		}

		$phpDoc = $this->extractPhpDoc($node);
		$tags = $this->extractTags($phpDoc);

		if (!$this->filter->filterFunctionTags($tags)) {
			return null;
		}

		assert($node->namespacedName !== null);
		$name = new NameInfo($node->namespacedName->toString());
		$info = new FunctionInfo($name, $task->primary);

		$info->description = $this->extractDescription($phpDoc);
		$info->tags = $tags;
		$info->file = $task->sourceFile;
		$info->startLine = $node->getStartLine();
		$info->endLine = $node->getEndLine();

		/** @var ?ReturnTagValueNode $returnTag */
		$returnTag = isset($tags['return'][0]) && $tags['return'][0] instanceof ReturnTagValueNode ? $tags['return'][0] : null;
		unset($tags['param'], $tags['return']);

		$info->genericParameters = $phpDoc->getAttribute('genericNameContext') ?? [];
		$info->parameters = $this->processParameters($this->extractParamTagValues($phpDoc), $node->params);
		$info->returnType = $returnTag ? $returnTag->type : $this->processTypeOrNull($node->returnType);
		$info->returnDescription = $returnTag?->description ?? '';
		$info->byRef = $node->byRef;

		$info->dependencies += $this->extractTypeDependencies($info->returnType);

		foreach ($info->tags['throws'] ?? [] as $tagValue) {
			assert($tagValue instanceof ThrowsTagValueNode);
			$info->dependencies += $this->extractTypeDependencies($tagValue->type);
		}

		foreach ($info->parameters as $parameterInfo) {
			$info->dependencies += $this->extractTypeDependencies($parameterInfo->type);
			$info->dependencies += $this->extractExprDependencies($parameterInfo->default);
		}

		foreach ($info->genericParameters as $genericParameter) {
			$info->dependencies += $this->extractTypeDependencies($genericParameter->bound);
		}

		if (!$this->filter->filterFunctionInfo($info)) {
			return null;
		}

		return $info;
	}


	/**
	 * @param  ParamTagValueNode[] $paramTags  indexed by [parameterName]
	 * @param  Node\Param[]        $parameters indexed by []
	 * @return ParameterInfo[]
	 */
	protected function processParameters(array $paramTags, array $parameters): array
	{
		$parameterInfos = [];
		foreach ($parameters as $position => $parameter) {
			assert($parameter->var instanceof Node\Expr\Variable);
			assert(is_scalar($parameter->var->name));

			$paramTag = $paramTags["\${$parameter->var->name}"] ?? null;
			$parameterInfo = new ParameterInfo($parameter->var->name, $position);
			$parameterInfo->description = $paramTag ? $paramTag->description : '';
			$parameterInfo->type = $paramTag ? $paramTag->type : $this->processTypeOrNull($parameter->type);
			$parameterInfo->byRef = $parameter->byRef;
			$parameterInfo->variadic = $parameter->variadic || ($paramTag && $paramTag->isVariadic);
			$parameterInfo->default = $this->processExprOrNull($parameter->default);

			$parameterInfos[$parameter->var->name] = $parameterInfo;
		}

		return $parameterInfos;
	}


	/**
	 * @param PhpDocTagValueNode[][] $tagValues indexed by [tagName][]
	 * @param string[]               $tagNames  indexed by []
	 */
	protected function processName(Node\Name $name, array $tagValues = [], array $tagNames = []): ClassLikeReferenceInfo
	{
		$refInfo = new ClassLikeReferenceInfo($name->toString());

		foreach ($tagNames as $tagName) {
			foreach ($tagValues[$tagName] ?? [] as $tagValue) {
				assert($tagValue instanceof ExtendsTagValueNode || $tagValue instanceof ImplementsTagValueNode || $tagValue instanceof UsesTagValueNode);

				$kind = $tagValue->type->type->getAttribute('kind');
				assert($kind instanceof IdentifierKind);

				if ($kind === IdentifierKind::ClassLike) {
					$refInfo = $tagValue->type->type->getAttribute('classLikeReference');
					assert($refInfo instanceof ClassLikeReferenceInfo);

					$refInfo->genericArgs = $tagValue->type->genericTypes;
				}
			}
		}

		return $refInfo;
	}


	/**
	 * @param  Node\Name[]            $names     indexed by []
	 * @param  PhpDocTagValueNode[][] $tagValues indexed by [tagName][]
	 * @param  string[]               $tagNames  indexed by []
	 * @return ClassLikeReferenceInfo[] indexed by [classLikeName]
	 */
	protected function processNameList(array $names, array $tagValues = [], array $tagNames = []): array
	{
		$nameMap = [];

		foreach ($names as $name) {
			$nameInfo = new ClassLikeReferenceInfo($name->toString());
			$nameMap[$nameInfo->fullLower] = $nameInfo;
		}

		foreach ($tagNames as $tagName) {
			foreach ($tagValues[$tagName] ?? [] as $tagValue) {
				assert($tagValue instanceof ExtendsTagValueNode || $tagValue instanceof ImplementsTagValueNode || $tagValue instanceof UsesTagValueNode);

				$kind = $tagValue->type->type->getAttribute('kind');
				assert($kind instanceof IdentifierKind);

				if ($kind === IdentifierKind::ClassLike) {
					$refInfo = $tagValue->type->type->getAttribute('classLikeReference');
					assert($refInfo instanceof ClassLikeReferenceInfo);

					$refInfo->genericArgs = $tagValue->type->genericTypes;
					$nameMap[$refInfo->fullLower] = $refInfo;
				}
			}
		}

		return $nameMap;
	}


	/**
	 * @param  PhpDocTagValueNode[] $values indexed by []
	 * @return ClassLikeReferenceInfo[] indexed by [classLikeName]
	 */
	protected function processMixinTags(array $values): array
	{
		$nameMap = [];

		foreach ($values as $value) {
			if ($value instanceof MixinTagValueNode && $value->type instanceof IdentifierTypeNode) {
				$kind = $value->type->getAttribute('kind');
				assert($kind instanceof IdentifierKind);

				if ($kind === IdentifierKind::ClassLike) {
					$refInfo = $value->type->getAttribute('classLikeReference');
					assert($refInfo instanceof ClassLikeReferenceInfo);

					$nameMap[$refInfo->fullLower] = $refInfo;
				}
			}
		}

		return $nameMap;
	}


	protected function processTypeOrNull(Identifier|Name|ComplexType|null $node): ?TypeNode
	{
		return $node ? $this->processType($node) : null;
	}


	protected function processType(Identifier|Name|ComplexType $node): TypeNode
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

		} elseif ($node instanceof Name && !$node->isSpecialClassName()) {
			$identifier = new IdentifierTypeNode($node->toString());
			$identifier->setAttribute('kind', IdentifierKind::ClassLike);
			$identifier->setAttribute('classLikeReference', new ClassLikeReferenceInfo($identifier->name));

		} else {
			$identifier = new IdentifierTypeNode($node->toString());
			$identifier->setAttribute('kind', IdentifierKind::Keyword);
		}

		return $identifier;
	}


	protected function processExprOrNull(?Node\Expr $expr): ?ExprInfo
	{
		return $expr ? $this->processExpr($expr) : null;
	}


	protected function processExpr(Node\Expr $expr): ExprInfo
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
			return new DimFetchExprInfo(
				$this->processExpr($expr->var),
				$this->processExpr($expr->dim),
			);

		} elseif ($expr instanceof Node\Expr\PropertyFetch) {
			return new PropertyFetchExprInfo(
				$this->processExpr($expr->var),
				$expr->name instanceof Node\Expr ? $this->processExpr($expr->name) : $expr->name->name,
			);

		} elseif ($expr instanceof Node\Expr\NullsafePropertyFetch) {
			return new NullSafePropertyFetchExprInfo(
				$this->processExpr($expr->var),
				$expr->name instanceof Node\Expr ? $this->processExpr($expr->name) : $expr->name->name,
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


	protected function processPhpDocExpr(ConstExprNode $expr): ExprInfo
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
					$item->key ? $this->processPhpDocExpr($item->key) : null,
					$this->processPhpDocExpr($item->value),
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
			throw new \LogicException(sprintf('Unsupported const expr node %s used in PHPDoc', get_debug_type($expr)));
		}
	}


	protected function extractPhpDoc(Node $node): PhpDocNode
	{
		return $node->getAttribute('phpDoc') ?? new PhpDocNode([]);
	}


	protected function extractDescription(PhpDocNode $node): string
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
	protected function extractTags(PhpDocNode $node): array
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
	 * @return ParamTagValueNode[] indexed by [parameterName]
	 */
	protected function extractParamTagValues(PhpDocNode $node): array
	{
		$values = [];

		foreach ($node->children as $child) {
			if ($child instanceof PhpDocTagNode && $child->value instanceof ParamTagValueNode) {
				$values[$child->value->parameterName] = $child->value;
			}
		}

		return $values;
	}


	/**
	 * @return ClassLikeReferenceInfo[] indexed by [classLike]
	 */
	protected function extractExprDependencies(?ExprInfo $expr): array
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

		} elseif ($expr instanceof DimFetchExprInfo) {
			$dependencies += $this->extractExprDependencies($expr->expr);
			$dependencies += $this->extractExprDependencies($expr->dim);

		} elseif ($expr instanceof PropertyFetchExprInfo || $expr instanceof NullSafePropertyFetchExprInfo) {
			$dependencies += $this->extractExprDependencies($expr->expr);
			$dependencies += is_string($expr->property) ? [] : $this->extractExprDependencies($expr->property);

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
	protected function extractTypeDependencies(?TypeNode $type): array
	{
		$dependencies = [];

		if ($type !== null) {
			foreach (PhpDocResolver::getIdentifiers($type) as $identifier) {
				if ($identifier->getAttribute('kind') === IdentifierKind::ClassLike) {
					$classLikeReference = $identifier->getAttribute('classLikeReference');
					assert($classLikeReference instanceof ClassLikeReferenceInfo);
					$dependencies[$classLikeReference->fullLower] = $classLikeReference;
				}
			}
		}

		return $dependencies;
	}


	protected function createDuplicateSymbolError(ClassLikeInfo | FunctionInfo $info, ClassLikeInfo | FunctionInfo $first): ErrorInfo
	{
		return new ErrorInfo(ErrorInfo::KIND_DUPLICATE_SYMBOL, implode("\n", [
			"Multiple definitions of {$info->name->full}.",
			"The first definition was found in {$first->file} on line {$first->startLine}",
			"and then another one was found in {$info->file} on line {$info->startLine}",
		]));
	}
}
