<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration\Theme;

use ApiGen\Configuration\ConfigurationException;
use ApiGen\Neon\NeonFile;
use Nette;


class ThemeConfig extends Nette\Object
{

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var ThemeConfigOptionsResolver
	 */
	private $themeConfigOptionsResolver;


	/**
	 * @param string $path
	 * @param ThemeConfigOptionsResolver $themeConfigOptionsResolver
	 */
	public function __construct($path, ThemeConfigOptionsResolver $themeConfigOptionsResolver)
	{
		if ( ! is_file($path)) {
			throw new ConfigurationException("File $path doesn't exist");
		}
		$this->path = $path;
		$this->themeConfigOptionsResolver = $themeConfigOptionsResolver;
	}


	/**
	 * @return array
	 */
	public function getOptions()
	{
		if ($this->options === NULL) {
			$file = new NeonFile($this->path);
			$values = $file->read();
			$values['templatesPath'] = dirname($this->path);
			$this->options = $this->themeConfigOptionsResolver->resolve($values);
		}
		return $this->options;
	}

}
