<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Analyzer\AnalyzeResult;
use ApiGenX\Analyzer\AnalyzeTask;
use ApiGenX\Analyzer\NodeVisitors\PhpDocResolver;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ConstantInfo;
use ApiGenX\Info\ErrorInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\MemberInfo;
use ApiGenX\Info\MethodInfo;
use ApiGenX\Info\NameInfo;
use ApiGenX\Info\ParameterInfo;
use ApiGenX\Info\PropertyInfo;
use ApiGenX\Info\TraitInfo;
use Iterator;
use Nette\Utils\FileSystem;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
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
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symfony\Component\Console\Helper\ProgressBar;


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

		/** @var ClassLikeInfo[] $found indexed by [classLikeName] */
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
					throw new \LogicException();
				}
			}

			$progressBar->setMessage($task->sourceFile);
			$progressBar->advance();
		}

		foreach ($missing as $fullLower => $dependencyOf) {
			$dependency = $dependencyOf->dependencies[$fullLower];
			$errors[ErrorInfo::KIND_MISSING_SYMBOL][] = new ErrorInfo(ErrorInfo::KIND_MISSING_SYMBOL, "Missing {$dependency->full}\nreferences by {$dependencyOf->name->full}");
			$found[$dependency->fullLower] = new ClassInfo($dependency, primary: false); // TODO: mark as missing (add MissingInfo?)
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
				yield $this->processClassLike($task, $node); // TODO: functions, constants, class aliases

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
		$name = $this->processName($node->namespacedName);

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

		} else {
			throw new \LogicException();
		}

		$classDoc = $this->extractPhpDoc($node);
		$info->description = $this->extractDescription($classDoc);
		$info->tags = $this->extractTags($classDoc);
		$info->file = $task->sourceFile;
		$info->startLine = $node->getStartLine();
		$info->endLine = $node->getEndLine();

		foreach ($this->extractMembers($info->tags, $node) as $member) {
			if ($member instanceof ConstantInfo){
				$info->constants[$member->name] = $member;
				$info->dependencies += $this->extractExprDependencies($member->value);

			} elseif ($member instanceof PropertyInfo){
				$info->properties[$member->name] = $member;
				$info->dependencies += $member->default ? $this->extractExprDependencies($member->default) : [];
				$info->dependencies += $member->type ? $this->extractTypeDependencies($member->type) : [];

			} elseif ($member instanceof MethodInfo){
				$info->methods[$member->nameLower] = $member;
				$info->dependencies += $member->returnType ? $this->extractTypeDependencies($member->returnType) : [];

				foreach ($member->parameters as $parameterInfo) {
					$info->dependencies += $parameterInfo->type ? $this->extractTypeDependencies($parameterInfo->type) : [];
					$info->dependencies += $parameterInfo->default ? $this->extractExprDependencies($parameterInfo->default) : [];
				}

			} else {
				throw new \LogicException();
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
					$memberInfo = new ConstantInfo($constant->name->name, $constant->value);

					$memberInfo->description = $description;
					$memberInfo->tags = $tags;

					$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
					$memberInfo->endLine = $member->getEndLine();

					$memberInfo->public = $member->isPublic();
					$memberInfo->protected = $member->isProtected();
					$memberInfo->private = $member->isPrivate();

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

					$memberInfo->type = $varTag ? $varTag->type : $this->processTypeOrNull($member->type);
					$memberInfo->default = $property->default;

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
			foreach ($tags[$tag] ?? [] as $value){
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
		foreach ($tags['method'] ?? [] as $value){
			$methodInfo = new MethodInfo($value->methodName);
			$methodInfo->magic = true;
			$methodInfo->public = true;
			$methodInfo->static = $value->isStatic;
			$methodInfo->returnType = $value->returnType;
			$methodInfo->description = $value->description;

			foreach ($value->parameters as $parameter) {
				$parameterInfo = new ParameterInfo($parameter->parameterName);
				$parameterInfo->type = $parameter->type;
				$parameterInfo->byRef = $parameter->isReference;
				$parameterInfo->variadic = $parameter->isVariadic;
//				$parameterInfo->default = $parameter->defaultValue; // TODO: implement expr format conversion

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
			$parameterInfo->default = $parameter->default;

			$parameterInfos[$parameter->var->name] = $parameterInfo;
		}

		return $parameterInfos;
	}


	private function processName(Node\Name $name): NameInfo
	{
		return new NameInfo($name->toString());
	}


	/**
	 * @param  Node\Name[] $names indexed by []
	 * @return NameInfo[] indexed by [classLikeName]
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


	/**
	 * @param null|Identifier|Name|NullableType|UnionType $node
	 */
	private function processTypeOrNull(?Node $node): ?TypeNode
	{
		return $node ? $this->processType($node) : null;
	}


	/**
	 * @param Identifier|Name|NullableType|UnionType $node
	 */
	private function processType(Node $node): TypeNode
	{
		if ($node instanceof NullableType) {
			return new NullableTypeNode($this->processType($node->type));
		}

		if ($node instanceof UnionType) {
			return new UnionTypeNode(array_map([$this, 'processType'], $node->types));
		}

		return new IdentifierTypeNode($node->toString());
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
	 * @return NameInfo[] indexed by [classLike]
	 */
	private function extractExprDependencies(Node\Expr $value): array
	{
		return []; // TODO!
	}


	/**
	 * @return NameInfo[] indexed by [classLike]
	 */
	private function extractTypeDependencies(TypeNode $type): array
	{
		$dependencies = [];

		foreach (PhpDocResolver::getIdentifiers($type) as $identifier) {
			$lower = strtolower($identifier->name);
			if (!isset(PhpDocResolver::KEYWORDS[$lower])) {
				$dependencies[$lower] = new NameInfo($identifier->name);
			}
		}

		return $dependencies;
	}
}
