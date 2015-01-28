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
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;


class TodoGenerator implements ConditionalTemplateGenerator
{

	const TODO = 'todo';

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
		$template = $this->templateFactory->createForType(TCO::TODO);
		$template = $this->setTodoElementsToTemplate($template);
		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption(CO::TODO);
	}


	/**
	 * @return Template
	 */
	private function setTodoElementsToTemplate(Template $template)
	{
		$todoElements = $this->elementExtractor->extractElementsByAnnotation(self::TODO);

		$template->setParameters([
			'todoClasses' => $todoElements[Elements::CLASSES],
			'todoInterfaces' => $todoElements[Elements::INTERFACES],
			'todoTraits' => $todoElements[Elements::TRAITS],
			'todoExceptions' => $todoElements[Elements::EXCEPTIONS],
			'todoConstants' => $todoElements[Elements::CONSTANTS],
			'todoMethods' => $todoElements[Elements::METHODS],
			'todoFunctions' => $todoElements[Elements::FUNCTIONS],
			'todoProperties' => $todoElements[Elements::PROPERTIES]
		]);

		return $template;
	}

}
