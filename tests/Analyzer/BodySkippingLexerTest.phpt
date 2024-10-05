<?php declare(strict_types = 1);

namespace ApiGenTests\Analyzer;

use ApiGen\Analyzer\BodySkippingLexer;
use ApiGenTests\Utils\SnapshotTester;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use SplFileInfo;
use Tester\Environment;
use Tester\TestCase;

use function is_array;
use function preg_replace;

require __DIR__ . '/../../vendor/autoload.php';


/**
 * @testCase
 */
class BodySkippingLexerTest extends TestCase
{
	/**
	 * @dataProvider providePostProcessTokensData
	 */
	public function testPostProcessTokens(SplFileInfo $file): void
	{
		$lexer = new BodySkippingLexer();
		$tokens = $lexer->tokenize(FileSystem::read($file->getRealPath()));
		$actualOutput = '';

		foreach ($tokens as $token) {
			if ($token->id !== 0) {
				$actualOutput .= $token->text;
			}
		}

		$actualOutput = Strings::replace($actualOutput, '#\h++$#m', '');
		$output = "{$file->getPath()}/{$file->getBasename('.in.php')}.out.php";
		SnapshotTester::assertSnapshotSame($output, $actualOutput);
	}


	/**
	 * @return iterable<string, array{SplFileInfo}>
	 */
	public function providePostProcessTokensData(): iterable
	{
		foreach (Finder::findFiles('*.in.php')->from(__DIR__ . '/Data/BodySkippingLexer') as $file) {
			yield $file->getRealPath() => [$file];
		}
	}
}


Environment::setup();
(new BodySkippingLexerTest)->run();
