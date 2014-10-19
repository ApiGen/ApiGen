<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\FileSystem\Finder;
use ApiGen\FileSystem\Zip;
use ApiGen\Generator\Generator;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Templating\Filters\AnnotationFilters;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Templating\TemplateFactory;
use Nette;
use Kdyby\Events\Subscriber;


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
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;

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
	 * @var Zip
	 */
	private $zip;

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
					CharsetConvertor $charsetConvertor,
					SourceFilters $sourceFilters,
					UrlFilters $urlFilters,
					Finder $finder,
					Zip $zip,
					AnnotationFilters $annotationFilters,
					RelativePathResolver $relativePathResolver
	) {
		$this->generator = $generator;
		$this->templateFactory = $templateFactory;
		$this->charsetConvertor = $charsetConvertor;
		$this->sourceFilters = $sourceFilters;
		$this->urlFilters = $urlFilters;
		$this->finder = $finder;
		$this->zip = $zip;
		$this->annotationFilters = $annotationFilters;
		$this->relativePathResolver = $relativePathResolver;
	}


	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'ApiGen\Configuration\Configuration::onSuccessValidate'
		);
	}


	/**
	 * @param array $config Validated config.
	 */
	public function onSuccessValidate($config)
	{
		$this->generator->setConfig($config);
		$this->templateFactory->setConfig($config);
		$this->relativePathResolver->setConfig($config);

		$this->charsetConvertor->setCharset($config['charset']);
		$this->finder->setConfig($config);
		$this->zip->setConfig($config);

		$this->sourceFilters->setConfig($config);
		$this->urlFilters->setConfig($config);
		$this->annotationFilters->setConfig($config);
	}

}
