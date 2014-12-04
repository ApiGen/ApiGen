<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Charset\CharsetDetector;
use ApiGen\Charset\Configuration\CharsetOptionsResolver;
use ApiGen\Configuration\Configuration;
use ApiGen\FileSystem\Finder;
use ApiGen\FileSystem\ZipArchiveGenerator;
use ApiGen\Generator\Generator;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Templating\Filters\AnnotationFilters;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Templating\TemplateFactory;
use Kdyby\Events\Subscriber;
use Nette;
use Nette\Utils\ArrayHash;


class InjectConfig extends Nette\Object implements Subscriber
{

	/**
	 * @var Generator
	 */
	private $generator;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var CharsetDetector
	 */
	private $charsetDetector;

	/**
	 * @var SourceFilters
	 */
	private $sourceFilters;

	/**
	 * @var UrlFilters
	 */
	private $urlFilters;

	/**
	 * @var Finder
	 */
	private $finder;

	/**
	 * @var AnnotationFilters
	 */
	private $annotationFilters;

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;


	public function __construct(
		Generator $generator,
		TemplateFactory $templateFactory,
		CharsetDetector $charsetDetector,
		SourceFilters $sourceFilters,
		UrlFilters $urlFilters,
		Finder $finder,
		AnnotationFilters $annotationFilters,
		RelativePathResolver $relativePathResolver
	) {
		$this->generator = $generator;
		$this->templateFactory = $templateFactory;
		$this->charsetDetector = $charsetDetector;
		$this->sourceFilters = $sourceFilters;
		$this->urlFilters = $urlFilters;
		$this->finder = $finder;
		$this->annotationFilters = $annotationFilters;
		$this->relativePathResolver = $relativePathResolver;
	}


	/**
	 * @return string[]
	 */
	public function getSubscribedEvents()
	{
		return ['ApiGen\Configuration\Configuration::onOptionsResolve'];
	}


	public function onOptionsResolve(array $config)
	{
		Configuration::$config = ArrayHash::from($config);

		$this->generator->setConfig($config);
		$this->templateFactory->setConfig($config);
		$this->relativePathResolver->setConfig($config);

		$this->charsetDetector->setCharsets($config['charset']);
		$this->finder->setConfig($config);

		$this->sourceFilters->setConfig($config);
		$this->urlFilters->setConfig($config);
		$this->annotationFilters->setConfig($config);
	}

}
