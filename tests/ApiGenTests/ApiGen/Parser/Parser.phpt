<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;
use Nette\Utils\Finder;
use Tester\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ParserTest extends TestCase
{

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var ParserResult
	 */
	private $parserResult;


	protected function setUp()
	{
		$this->container = createContainer();
		$this->parser = $this->container->getByType('ApiGen\Parser\Parser');
		$this->parserResult = $this->container->getByType('ApiGen\Parser\ParserResult');
		$this->setupConfigDefaults(); // required by Broker
	}


	public function testParseClasses()
	{
		$files = $this->getFilesFromDir(PROJECT_DIR);
		Assert::count(14, $files);

		Assert::count(0, $this->parserResult->getClasses());
		$this->parser->parse($files);
		Assert::count(16, $this->parserResult->getClasses());
	}


	/**
	 * @param string $dir
	 * @return array { filePath => size }
	 */
	private function getFilesFromDir($dir)
	{
		$files = [];
		foreach (Finder::find('*.php')->in($dir) as $splFile) {
			$files[] = $splFile;
		}
		return $files;
	}


	private function setupConfigDefaults()
	{
		// ApiGen root path
		define('APIGEN_ROOT_PATH', __DIR__ . '/../../../../src');

		/** @var Configuration $configuration */
		$configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$defaults['source'] = [PROJECT_DIR];
		$defaults['destination'] = API_DIR;
		$defaults = $configuration->resolveOptions($defaults);
		Configuration::$config = ArrayHash::from($defaults);
	}
}


(new ParserTest)->run();
