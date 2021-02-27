<?php declare(strict_types = 1);

namespace ApiGenX\Tasks;

use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ConstantInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\MethodInfo;
use ApiGenX\Info\NameInfo;
use ApiGenX\Info\ParameterInfo;
use ApiGenX\Info\PropertyInfo;
use ApiGenX\Info\TraitInfo;
use ApiGenX\TaskExecutor\Task;
use ApiGenX\TaskExecutor\TaskEnvironment;
use ApiGenX\Tasks\NodeVisitors\BodySkipper;
use ApiGenX\Tasks\NodeVisitors\PhpDocResolver;
use Iterator;
use Nette\Utils\FileSystem;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;


final class AnalyzeTask implements Task
{
	private string $sourceFile;

	private bool $isPrimary;


	public function __construct(string $sourceFile, bool $isPrimary = true)
	{
		$this->sourceFile = $sourceFile;
		$this->isPrimary = $isPrimary;
	}


	public function run(TaskEnvironment $environment)
	{
		$environment['phpParser'] ??= $this->createPhpParser();
		$environment['phpAstTraverser'] ??= $this->createAstTraverser();

		try {
			$ast = $environment['phpParser']->parse(FileSystem::read($this->sourceFile));
			$ast = $environment['phpAstTraverser']->traverse($ast);

		} catch (\PhpParser\Error $e) {
			return []; // TODO: emit ErrorInfo
		}

		return iterator_to_array($this->processNodes($ast), false);
	}


	/**
	 * @param Node[] $nodes
	 */
	private function processNodes(array $nodes): Iterator // TODO: move to astTraverser?
	{
		foreach ($nodes as $node) {
			if ($node instanceof Node\Stmt\Namespace_) {
				yield from $this->processNodes($node->stmts); // TODO: emit NamespaceInfo?

			} elseif ($node instanceof Node\Stmt\ClassLike && $node->name !== null) {
				yield $this->processClassLike($node); // TODO: functions, constants, class aliases

			} elseif ($node instanceof Node) {
				foreach ($node->getSubNodeNames() as $name) {
					$subNode = $node->$name;

					if (is_array($subNode)) {
						yield from $this->processNodes($subNode);

					} elseif ($subNode instanceof Node) {
						yield from $this->processNodes([$subNode]);
					}
				}
			}
		}
	}


	private function processClassLike(Node\Stmt\ClassLike $node): ClassLikeInfo // TODO: handle trait usage
	{
		$name = $this->processName($node->namespacedName);

		if ($node instanceof Node\Stmt\Class_) {
			$info = new ClassInfo($name);
			$info->abstract = $node->isAbstract();
			$info->final = $node->isFinal();
			$info->extends = $node->extends ? $this->processName($node->extends) : null;
			$info->implements = $this->processNameList($node->implements);
			$info->dependencies += $info->extends ? [$info->extends->fullLower => $info->extends] : [];
			$info->dependencies += $info->implements;

		} elseif ($node instanceof Node\Stmt\Interface_) {
			$info = new InterfaceInfo($name);
			$info->extends = $this->processNameList($node->extends);
			$info->dependencies += $info->extends;

		} elseif ($node instanceof Node\Stmt\Trait_) {
			$info = new TraitInfo($name);

		} else {
			throw new \LogicException();
		}

		$classDoc = $this->extractPhpDoc($node);
		$info->primary = $this->isPrimary;
		$info->description = $this->extractDescription($classDoc);
		$info->tags = $this->extractTags($classDoc);
		$info->file = $this->sourceFile;
		$info->startLine = $node->getStartLine();
		$info->endLine = $node->getEndLine();

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

					$info->constants[$constant->name->name] = $memberInfo;
					$info->dependencies += $this->extractExprDependencies($constant->value);
				}

			} elseif ($member instanceof Node\Stmt\Property) {
				$type = isset($tags['var'][0]) ? $tags['var'][0]->type : $this->processType($member->type);

				foreach ($member->props as $property) {
					$memberInfo = new PropertyInfo($property->name->name);

					$memberInfo->description = isset($tags['var'][0]) ? $tags['var'][0]->description : $description;
					$memberInfo->tags = $tags;

					$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
					$memberInfo->endLine = $member->getEndLine();

					$memberInfo->public = $member->isPublic();
					$memberInfo->protected = $member->isProtected();
					$memberInfo->private = $member->isPrivate();
					$memberInfo->static = $member->isStatic();

					$memberInfo->type = $type;
					$memberInfo->default = $property->default;

					$info->properties[$property->name->name] = $memberInfo;
					$info->dependencies += $property->default ? $this->extractExprDependencies($property->default) : [];
					$info->dependencies += $type ? $this->extractTypeDependencies($type) : [];
				}

			} elseif ($member instanceof Node\Stmt\ClassMethod) {
				$memberInfo = new MethodInfo($member->name->name);

				$memberInfo->description = $description;
				$memberInfo->tags = $tags;

				$memberInfo->parameters = $this->processParameters($memberDoc->getParamTagValues(), $member->params);
				$memberInfo->returnType = isset($tags['return'][0]) ? $tags['return'][0]->type : $this->processType($member->returnType);
				$memberInfo->byRef = $member->byRef;

				$memberInfo->startLine = $member->getComments() ? $member->getComments()[0]->getStartLine() : $member->getStartLine();
				$memberInfo->endLine = $member->getEndLine();

				$memberInfo->public = $member->isPublic();
				$memberInfo->protected = $member->isProtected();
				$memberInfo->private = $member->isPrivate();

				$memberInfo->static = $member->isStatic();
				$memberInfo->abstract = $member->isAbstract();
				$memberInfo->final = $member->isFinal();

				$info->methods[$memberInfo->nameLower] = $memberInfo;
				$info->dependencies += $memberInfo->returnType ? $this->extractTypeDependencies($memberInfo->returnType) : [];

				foreach ($memberInfo->parameters as $parameterInfo) {
					$info->dependencies += $parameterInfo->type ? $this->extractTypeDependencies($parameterInfo->type) : [];
					$info->dependencies += $parameterInfo->default ? $this->extractExprDependencies($parameterInfo->default) : [];
				}
			}
		}

		return $info;
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
			$paramTag = $paramTags["\${$parameter->var->name}"] ?? null;
			$parameterInfo = new ParameterInfo($parameter->var->name);
			$parameterInfo->description = $paramTag ? $paramTag->description : '';
			$parameterInfo->type = $paramTag ? $paramTag->type : $this->processType($parameter->type);
			$parameterInfo->byRef = $parameter->byRef;
			$parameterInfo->variadic = $parameter->variadic || ($paramTag && $paramTag->isVariadic);
			$parameterInfo->default = $parameter->default;

			$parameterInfos[$parameter->var->name] = $parameterInfo;
		}

		return $parameterInfos;
	}


	private function processName(Node\Name $name): NameInfo
	{
		return new NameInfo($name->toString()); // TODO: utilize already parsed structure?
	}


	/**
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
	private function processType(?Node $node): ?TypeNode
	{
		if ($node === null) {
			return null;
		}

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


	private function extractExprDependencies(Node\Expr $value): array
	{
		return []; // TODO!
	}


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


	private function createPhpParser(): Parser
	{
		return (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
	}


	private function createAstTraverser(): NodeTraverser
	{
		$nameResolver = new NameResolver();
		$nameContext = $nameResolver->getNameContext();

		$phpDocLexer = new Lexer();
		$phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());

		$traverser = new NodeTraverser();
		$traverser->addVisitor(new BodySkipper());
		$traverser->addVisitor($nameResolver);
		$traverser->addVisitor(new PhpDocResolver($phpDocLexer, $phpDocParser, $nameContext));

		return $traverser;
	}
}
