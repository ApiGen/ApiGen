<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;
use Nette\Utils\Finder;
use Tester\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ParserTest extends TestCase
{

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var Container
	 */
	private $container;


	protected function setUp()
	{
		$this->container = createContainer();
		$this->parser = $this->container->getByType('ApiGen\Parser\Parser');
		$this->setupConfigDefaults();
	}


	public function testType()
	{
		Assert::type(
			'ApiGen\Parser\Parser',
			$this->parser
		);
	}


	public function testParseClasses()
	{
		$files = $this->getFilesFromDir(PROJECT_DIR);
		$this->parser->parse($files);

		$classes = $this->parser->getClasses();
		Assert::count(17, $classes);
	}


	/**
	 * @param string $dir
	 * @return array { filePath => size }
	 */
	private function getFilesFromDir($dir)
	{
		$files = array();
		foreach (Finder::find('*.php')->in($dir) as $splFile) {
			/** @var \SplFileInfo $splFile */
			$files[$dir . DS . $splFile->getFilename()] = $splFile->getSize();
		}
		return $files;
	}


	private function setupConfigDefaults()
	{
		// ApiGen root path
		define('APIGEN_ROOT_PATH', __DIR__ . '/../../../../src');

		/** @var Configuration $configuration */
		$configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$defaults['source'] = array(PROJECT_DIR);
		$defaults['destination'] = API_DIR;
		$defaults = $configuration->setDefaults($defaults);
		Configuration::$config = ArrayHash::from($defaults);
	}
}


\run(new ParserTest);
