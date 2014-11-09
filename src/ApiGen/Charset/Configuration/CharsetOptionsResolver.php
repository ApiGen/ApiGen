<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Charset\Configuration;

use ApiGen\Charset\Encoding;
use ApiGen\Configuration\OptionsResolverFactory;
use Nette;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CharsetOptionsResolver extends Nette\Object
{

	/**
	 * @var array
	 */
	private $defaults = array(
		'charsets' => array(
			Encoding::UTF_8,
			Encoding::WIN_1251,
			Encoding::WIN_1252,
			Encoding::ISO_8859_1,
			Encoding::ISO_8859_2,
			Encoding::ISO_8859_3,
			Encoding::ISO_8859_4,
			Encoding::ISO_8859_5,
			Encoding::ISO_8859_6,
			Encoding::ISO_8859_7,
			Encoding::ISO_8859_8,
			Encoding::ISO_8859_9,
			Encoding::ISO_8859_10,
			Encoding::ISO_8859_13,
			Encoding::ISO_8859_14,
			Encoding::ISO_8859_15
		)
	);

	/**
	 * @var OptionsResolverFactory
	 */
	private $optionsResolverFactory;

	/**
	 * @var OptionsResolver
	 */
	private $resolver;


	public function __construct(OptionsResolverFactory $optionsResolverFactory)
	{
		$this->optionsResolverFactory = $optionsResolverFactory;
	}


	/**
	 * @return array
	 */
	public function resolve(array $options = array())
	{
		$this->resolver = $this->optionsResolverFactory->create();
		$this->setDefaults();
		$this->setAllowedTypes();
		$this->setNormalizers();
		$options = $this->resolver->resolve($options);
		return $options['charsets'];
	}


	private function setDefaults()
	{
		$this->resolver->setDefaults($this->defaults);
	}


	private function setAllowedTypes()
	{
		$this->resolver->setAllowedTypes(array(
			'charsets' => 'array'
		));
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizers([
			'charsets' => function (Options $options, $value) {
				$value = array_map('strtoupper', $value);
				$value = $this->replaceWin1250WithIso($value);
				$value = $this->filterSupportedCharsets($value);
				$value = $this->moveUtfFirst($value);
				return $value;
			}
		]);
	}


	/**
	 * @return array
	 */
	private function replaceWin1250WithIso(array $charsets)
	{
		var_dump($charsets);
		if (($key = array_search(Encoding::WIN_1250, $charsets)) !== FALSE) {
			$charsets[$key] = Encoding::ISO_8859_1;
		}
		return $charsets;
	}


	/**
	 * @return array
	 */
	private function filterSupportedCharsets(array $charsets)
	{
		$supportedEncodingList = array_map('strtoupper', mb_list_encodings());
		return array_intersect($charsets, $supportedEncodingList);
	}


	/**
	 * @return array
	 */
	private function moveUtfFirst(array $charsets)
	{
		if (($key = array_search(Encoding::UTF_8, $charsets)) !== FALSE) {
			unset($charsets[$key]);
		}
		array_unshift($charsets, Encoding::UTF_8);
		return $charsets;
	}

}
