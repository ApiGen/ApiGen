<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Generator\ConditionalTemplateGenerator;
use ApiGen\Parser\Elements\ElementExtractor;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;


class DeprecatedGenerator implements ConditionalTemplateGenerator
{

	const DEPRECATED = 'deprecated';

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ElementExtractor
	 */
	private $elementExtractor;


	public function __construct(
		Configuration $configuration,
		TemplateFactory $templateFactory,
		ElementExtractor $elementExtractor
	) {
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->elementExtractor = $elementExtractor;
	}


	public function generate()
	{
		$template = $this->templateFactory->createForType(TCO::DEPRECATED);
		$template = $this->setDeprecatedElementsToTemplate($template);
		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption(CO::DEPRECATED);
	}


	/**
	 * @return Template
	 */
	private function setDeprecatedElementsToTemplate(Template $template)
	{
		$skipClassCallback = function (ReflectionClass $class) {
			return $class->isDeprecated();
		};
		$deprecatedElements = $this->elementExtractor->extractElementsByAnnotation(
			self::DEPRECATED, $skipClassCallback
		);

		$template->setParameters([
			'deprecatedClasses' => $deprecatedElements[Elements::CLASSES],
			'deprecatedInterfaces' => $deprecatedElements[Elements::INTERFACES],
			'deprecatedTraits' => $deprecatedElements[Elements::TRAITS],
			'deprecatedExceptions' => $deprecatedElements[Elements::EXCEPTIONS],
			'deprecatedConstants' => $deprecatedElements[Elements::CONSTANTS],
			'deprecatedMethods' => $deprecatedElements[Elements::METHODS],
			'deprecatedFunctions' => $deprecatedElements[Elements::FUNCTIONS],
			'deprecatedProperties' => $deprecatedElements[Elements::PROPERTIES]
		]);

		return $template;
	}

}
