<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Charset\CharsetDetector;
use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Templating\Filters\AnnotationFilters;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Templating\Filters\UrlFilters;
use Kdyby\Events\Subscriber;
use Nette\Utils\ArrayHash;


class InjectConfig implements Subscriber
{

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
	 * @var AnnotationFilters
	 */
	private $annotationFilters;

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;


	public function __construct(
		CharsetDetector $charsetDetector,
		SourceFilters $sourceFilters,
		UrlFilters $urlFilters,
		AnnotationFilters $annotationFilters,
		RelativePathResolver $relativePathResolver
	) {
		$this->charsetDetector = $charsetDetector;
		$this->sourceFilters = $sourceFilters;
		$this->urlFilters = $urlFilters;
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

		$this->relativePathResolver->setConfig($config);

		$this->charsetDetector->setCharsets($config['charset']);

		$this->sourceFilters->setConfig($config);
		$this->urlFilters->setConfig($config);
		$this->annotationFilters->setConfig($config);
	}

}
