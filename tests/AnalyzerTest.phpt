<?php declare(strict_types = 1);

namespace ApiGenTests;

use ApiGen\Analyzer\AnalyzeTask;
use ApiGen\Analyzer\AnalyzeTaskHandler;
use ApiGen\Analyzer\BodySkippingLexer;
use ApiGen\Analyzer\Filter;
use ApiGen\Analyzer\NodeVisitors\PhpDocResolver;
use ApiGen\Info\NameInfo;
use Nette\Neon\Node;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PhpParser;
use PHPStan;
use SplFileInfo;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;
use UnitEnum;


require __DIR__ . '/../vendor/autoload.php';


/**
 * @testCase
 * @phpIni short_open_tag = 1
 */
class AnalyzerTest extends TestCase
{
	/**
	 * @dataProvider provideSnapshotsData
	 */
	public function testSnapshots(SplFileInfo $file): void
	{
		$taskHandler = $this->createAnalyzeTaskHandler();
		$result = $taskHandler->handle(new AnalyzeTask($file->getRealPath(), primary: true));
		$serialized = self::dump($result) . "\n";
		$serialized = str_replace(dirname(__DIR__), '%rootDir%', $serialized);

		$output = "{$file->getPath()}/{$file->getBasename('.php')}.neon";

		if (is_file($output) || getenv('CI')) {
			$actual = $serialized;
			$expected = FileSystem::read($output);
			Assert::same($expected, $actual);

		} else {
			FileSystem::write($output, $serialized);
		}
	}


	public function provideSnapshotsData(): iterable
	{
		foreach (Finder::findFiles('*.php')->from(__DIR__ . '/Data') as $file) {
			yield $file->getRealPath() => [$file];
		}
	}


	private function createAnalyzeTaskHandler(): AnalyzeTaskHandler
	{
		$phpParserFactory = new PhpParser\ParserFactory();
		$phpLexer = new BodySkippingLexer();
		$phpParser = $phpParserFactory->create(PhpParser\ParserFactory::PREFER_PHP7, $phpLexer);

		$traverser = new PhpParser\NodeTraverser();
		$nameResolver = new PhpParser\NodeVisitor\NameResolver();

		$phpDocLexer = new PHPStan\PhpDocParser\Lexer\Lexer();
		$phpDocExprParser = new PHPStan\PhpDocParser\Parser\ConstExprParser();
		$phpDocTypeParser = new PHPStan\PhpDocParser\Parser\TypeParser($phpDocExprParser);
		$phpDocParser = new PHPStan\PhpDocParser\Parser\PhpDocParser($phpDocTypeParser, $phpDocExprParser);
		$phpDocResolver = new PhpDocResolver($phpDocLexer, $phpDocParser, $nameResolver->getNameContext());

		$traverser->addVisitor($nameResolver);
		$traverser->addVisitor($phpDocResolver);

		$filter = new Filter(excludeProtected: false, excludePrivate: true, excludeTagged: []);

		return new AnalyzeTaskHandler($phpParser, $traverser, $filter);
	}


	private static function dump(mixed $value, string $indentation = ''): string
	{
		if (is_object($value)) {
			if ($value instanceof NameInfo) {
				return self::dump($value->full);
			}

			if ($value instanceof UnitEnum) {
				return self::dump($value->name);
			}

			$classRef = new \ReflectionClass($value);
			$name = $classRef->getShortName();
			$name = str_ends_with($name, 'Info') ? substr($name, 0, -4) : $name;
			$name = str_ends_with($name, 'Node') ? substr($name, 0, -4) : $name;
			$s = "@$name(\n";

			foreach ($classRef->getProperties() as $propertyRef) {
				$k = $propertyRef->getName();
				$v = $propertyRef->getValue($value);

				if ($k === 'startLine' || $k === 'endLine' || $k === 'fullLower' || $k === 'nameLower') {
					continue;
				}

				if ($propertyRef->hasDefaultValue() && $propertyRef->getDefaultValue() === $v) {
					continue;
				}

				if ($propertyRef->isPromoted()) {
					$constructorRef = $classRef->getConstructor();

					foreach ($constructorRef?->getParameters() ?? [] as $parameterRef) {
						if ($parameterRef->getName() === $k && $parameterRef->isDefaultValueAvailable() && $parameterRef->getDefaultValue() === $v) {
							continue 2;
						}
					}
				}

				$s .= "$indentation  $k: " . self::dump($v, $indentation . '  ') . "\n";
			}

			$s .= "$indentation)";

			return $s;

		} elseif (is_array($value)) {
			if (array_is_list($value)) {
				if ($value === []) {
					return '[]';

				} else {
					$s = "[\n";

					foreach ($value as $item) {
						$s .= "$indentation  " . self::dump($item, $indentation . '  ') . ",\n";
					}

					$s .= "$indentation]";

					return $s;
				}

			} else {
				$s = '{';

				foreach ($value as $k => $v) {
					$s .= "\n$indentation  $k: " . self::dump($v, $indentation . '  ');
				}

				$s .= "\n$indentation}";

				return $s;
			}

		} elseif (is_string($value)) {
			return (new Node\StringNode($value))->toString();

		} else {
			return (new Node\LiteralNode($value))->toString();
		}
	}
}


Environment::setup();
(new AnalyzerTest)->run();
