<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Charset\CharsetDetector;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use Kdyby\Events\Subscriber;


class InjectConfig implements Subscriber
{

	/**
	 * @var CharsetDetector
	 */
	private $charsetDetector;

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;


	public function __construct(CharsetDetector $charsetDetector, RelativePathResolver $relativePathResolver)
	{
		$this->charsetDetector = $charsetDetector;
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
		$this->relativePathResolver->setConfig($config);
		$this->charsetDetector->setCharsets($config['charset']);
	}

}
