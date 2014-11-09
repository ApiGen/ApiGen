<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration\Theme;

use ApiGen\Configuration\ConfigurationException;
use ApiGen\Configuration\OptionsResolverFactory;
use Nette;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ThemeConfigOptionsResolver extends Nette\Object
{

	/**
	 * @var array
	 */
	private $defaults = array(
		'name' => '',
		'options' => array(
			'elementDetailsCollapsed' => TRUE,
			'elementsOrder' => 'natural' # or: alphabetical
		),
		'resources' => array(
			'resources' => 'resources'
		),
		'templates' => array(
			'overview' => array(
				'filename' => 'index.html',
				'template' => 'overview.latte'
			),
			'combined' => array(
				'filename' => 'resources/combined.js',
				'template' => 'combined.js.latte'
			),
			'elementlist' => array(
				'filename' => 'elementlist.js',
				'template' => 'elementlist.js.latte'
			),
			'404' => array(
				'filename' => '404.html',
				'template' => '404.latte'
			),
			'package' => array(
				'filename' => 'package-%s.html',
				'template' => 'package.latte'
			),
			'namespace' => array(
				'filename' => 'namespace-%s.html',
				'template' => 'namespace.latte'
			),
			'class' => array(
				'filename' => 'class-%s.html',
				'template' => 'class.latte'
			),
			'constant' => array(
				'filename' => 'constant-%s.html',
				'template' => 'constant.latte'
			),
			'function' => array(
				'filename' => 'function-%s.html',
				'template' => 'function.latte'
			),
			'source' => array(
				'filename' => 'source-%s.html',
				'template' => 'source.latte'
			),
			'tree' => array(
				'filename' => 'tree.html',
				'template' => 'tree.latte'
			),
			'deprecated' => array(
				'filename' => 'deprecated.html',
				'template' => 'deprecated.latte'
			),
			'todo' => array(
				'filename' => 'todo.html',
				'template' => 'todo.latte'
			),
			'sitemap' => array(
				'filename' => 'sitemap.xml',
				'template' => 'sitemap.xml.latte'
			),
			'opensearch' => array(
				'filename' => 'opensearch.xml',
				'template' => 'opensearch.xml.latte'
			),
			'robots' => array(
				'filename' => 'robots.txt',
				'template' => 'robots.txt.latte'
			)
		),
		'templatesPath' => ''
	);

	/**
	 * @var OptionsResolver
	 */
	private $resolver;

	/**
	 * @var OptionsResolverFactory
	 */
	private $optionsResolverFactory;


	public function __construct(OptionsResolverFactory $optionsResolverFactory)
	{
		$this->optionsResolverFactory = $optionsResolverFactory;
	}


	/**
	 * @return array
	 */
	public function resolve(array $options)
	{
		$this->resolver = $this->optionsResolverFactory->create();
		$this->setDefaults();
		$this->setAllowedValues();
		$this->setNormalizers();
		return $this->resolver->resolve($options);
	}


	private function setDefaults()
	{
		$this->resolver->setDefaults($this->defaults);
	}


	private function setAllowedValues()
	{
		$this->resolver->setAllowedValues(array(
			'templates' => function ($value) {
				foreach ($value as $type => $settings) {
					$this->validateFileExistence($settings['template'], $type);
				}
				return TRUE;
			}
		));
	}


	/**
	 * @param string $file
	 * @param string $type
	 */
	private function validateFileExistence($file, $type)
	{
		if ( ! is_file($file)) {
			throw new ConfigurationException("Template for $type was not found in $file");
		}
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizers(array(
			'templates' => function (Options $options, $value) {
				return $this->makeTemplatePathsAbsolute($value, $options);
			}
		));
	}


	/**
	 * @return array
	 */
	private function makeTemplatePathsAbsolute(array $value, Options $options)
	{
		foreach ($value as $type => $settings) {
			$value[$type]['template'] = $options['templatesPath'] . DS . $settings['template'];
		}
		return $value;
	}

}
