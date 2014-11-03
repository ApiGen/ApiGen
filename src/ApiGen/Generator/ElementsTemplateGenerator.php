<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Elements\Elements;
use ApiGen\Elements\ElementStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use Nette;
use stdClass;


class ElementsTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var SourceCodeGenerator
	 */
	private $sourceCodeGenerator;

	/**
	 * @var Elements
	 */
	private $elements;


	public function __construct(
		Elements $elements,
		ElementStorage $elementStorage,
		TemplateFactory $templateFactory,
		SourceCodeGenerator $sourceCodeGenerator
	) {
		$this->templateFactory = $templateFactory;
		$this->elementStorage = $elementStorage;
		$this->sourceCodeGenerator = $sourceCodeGenerator;
		$this->elements = $elements;
	}


	public function generate()
	{
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			/** @var ReflectionElement $element */
			foreach ($elementList as $element) {
				$template = $this->createTemplateForType($type, $element);
				$template = $this->addElementsPackageToTemplate($template, $element);

				if ($element instanceof ReflectionClass) {
					/** @var ReflectionClass $element */

					// Class
					$template->tree = array_merge(array_reverse($element->getParentClasses()), array($element));
					$template->directSubClasses = $element->getDirectSubClasses();
					$template->indirectSubClasses = $element->getIndirectSubClasses();
					$template->directImplementers = $element->getDirectImplementers();
					$template->indirectImplementers = $element->getIndirectImplementers();
					$template->directUsers = $element->getDirectUsers();
					$template->indirectUsers = $element->getIndirectUsers();

					$template->class = $element;
					$template->save($template->classUrl($element));

				} elseif ($element instanceof ReflectionConstant) {
					// Constant
					$template->constant = $element;
					$template->save($template->constantUrl($element));

				} elseif ($element instanceof ReflectionFunction) {
					// Function
					$template->function = $element;
					$template->save($template->functionUrl($element));
				}



				// todo: split to source code generator

				// Generate source codes
				if ($element->isTokenized()) {
					// todo: create new template
					$this->sourceCodeGenerator->generateForElement($template, $element);
				}





			}
		}
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return TRUE;
	}


	/**
	 * @param string $type
	 * @param ReflectionElement $element
	 * @return Template|stdClass
	 */
	private function createTemplateForType($type, ReflectionElement $element)
	{
		if ($type === Elements::CONSTANTS) {
			return $this->templateFactory->createNamedForElement('constant', $element);

		} elseif ($type === Elements::FUNCTIONS) {
			return $this->templateFactory->createNamedForElement('function', $element);

		} elseif (in_array($type, $this->elements->getClassTypeList())) {
			return $this->templateFactory->createNamedForElement('class', $element);
		}
	}


	/**
	 * @param Template|stdClass $template
	 * @param ReflectionElement $element
	 * @return Template|stdClass
	 */
	private function addElementsPackageToTemplate(Template $template, ReflectionElement $element)
	{
		if ($this->elementStorage->hasNamespaces()) {
			$template->namespace = $name = $element->getPseudoNamespaceName();
			$packages = $this->elementStorage->getNamespaces();

		} else {
			$template->package = $name = $element->getPseudoPackageName();
			$packages = $this->elementStorage->getPackages();
		}

		$template->classes = $packages[$name][Elements::CLASSES];
		$template->interfaces = $packages[$name][Elements::INTERFACES];
		$template->traits = $packages[$name][Elements::TRAITS];
		$template->exceptions = $packages[$name][Elements::EXCEPTIONS];
		$template->constants = $packages[$name][Elements::CONSTANTS];
		$template->functions = $packages[$name][Elements::FUNCTIONS];

		return $template;
	}

}
