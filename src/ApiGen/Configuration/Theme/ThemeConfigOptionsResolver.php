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


class ThemeConfigOptionsResolver extends Nette\Object
{

	/**
	 * @var array
	 */
	private $defaults = [
		'name' => '',
		'options' => [
			'elementDetailsCollapsed' => TRUE,
			'elementsOrder' => 'natural' # or: alphabetical
		],
		'resources' => [
			'resources' => 'resources'
		],
		'templates' => [
			'overview' => [
				'filename' => 'index.html',
				'template' => 'overview.latte'
			],
			'combined' => [
				'filename' => 'resources/combined.js',
				'template' => 'combined.js.latte'
			],
			'elementlist' => [
				'filename' => 'elementlist.js',
				'template' => 'elementlist.js.latte'
			],
			'404' => [
				'filename' => '404.html',
				'template' => '404.latte'
			],
			'package' => [
				'filename' => 'package-%s.html',
				'template' => 'package.latte'
			],
			'namespace' => [
				'filename' => 'namespace-%s.html',
				'template' => 'namespace.latte'
			],
			'class' => [
				'filename' => 'class-%s.html',
				'template' => 'class.latte'
			],
			'constant' => [
				'filename' => 'constant-%s.html',
				'template' => 'constant.latte'
			],
			'function' => [
				'filename' => 'function-%s.html',
				'template' => 'function.latte'
			],
			'source' => [
				'filename' => 'source-%s.html',
				'template' => 'source.latte'
			],
			'tree' => [
				'filename' => 'tree.html',
				'template' => 'tree.latte'
			],
			'deprecated' => [
				'filename' => 'deprecated.html',
				'template' => 'deprecated.latte'
			],
			'todo' => [
				'filename' => 'todo.html',
				'template' => 'todo.latte'
			],
			'sitemap' => [
				'filename' => 'sitemap.xml',
				'template' => 'sitemap.xml.latte'
			],
			'opensearch' => [
				'filename' => 'opensearch.xml',
				'template' => 'opensearch.xml.latte'
			],
			'robots' => [
				'filename' => 'robots.txt',
				'template' => 'robots.txt.latte'
			]
		],
		'templatesPath' => ''
	];

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
		$this->resolver->setAllowedValues([
			'templates' => function ($value) {
				foreach ($value as $type => $settings) {
					$this->validateFileExistence($settings['template'], $type);
				}
				return TRUE;
			}
		]);
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
		$this->resolver->setNormalizers([
			'resources' => function (Options $options, $resources) { // todo: make same logic as for templates
				$absolutizedResources = array();
				foreach ($resources as $key => $resource) {
					$key = $options['templatesPath'] . '/' . $key;
					$absolutizedResources[$key] = $resource;
				}
				return $absolutizedResources;
			},
			'templates' => function (Options $options, $value) {
				return $this->makeTemplatePathsAbsolute($value, $options);
			}
		]);
	}


	/**
	 * @return array
	 */
	private function makeTemplatePathsAbsolute(array $value, Options $options)
	{
		foreach ($value as $type => $settings) {
			$value[$type]['template'] = $options['templatesPath'] . '/' . $settings['template'];
		}
		return $value;
	}

}
