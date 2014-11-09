<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Elements\ElementFilter;
use ApiGen\Elements\ElementSorter;
use ApiGen\Elements\ElementStorage;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Templating\TemplateNavigator;
use Nette;


/**
 * Note: this is almost identical with @see PackagesTemplateGenerator
 */
class NamespacesTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;


	public function __construct(
		ElementStorage $elementStorage,
		TemplateFactory $templateFactory,
		TemplateNavigator $templateNavigator
	) {
		$this->elementStorage = $elementStorage;
		$this->templateFactory = $templateFactory;
		$this->templateNavigator = $templateNavigator;
	}


	public function generate()
	{
		foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
			$this->generateForNamespace($name, $namespace);
		}
	}


	/**
	 * @param string $name
	 * @param array $namespace
	 */
	private function generateForNamespace($name, $namespace)
	{
		$template = $this->templateFactory->createNamedForElement('namespace', $namespace);
		$template->namespace = $name;
		$template->subnamespaces = $this->getSubnamespacesForNamespace($template->namespaces, $name);

		$template->classes = $namespace['classes'];
		$template->interfaces = $namespace['interfaces'];
		$template->traits = $namespace['traits'];
		$template->exceptions = $namespace['exceptions'];
		$template->constants = $namespace['constants'];
		$template->functions = $namespace['functions'];

		$template->save();
	}


	/**
	 * @param array $namespaces
	 * @param string $namespaceName
	 * @return array
	 */
	private function getSubnamespacesForNamespace($namespaces, $namespaceName)
	{
		array_filter($namespaces, function ($subnamespaceName) use ($namespaceName) {
			return (bool) preg_match('~^' . preg_quote($namespaceName) . '\\\\[^\\\\]+$~', $subnamespaceName);
		});
		return $namespaces;
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		if (count($this->elementStorage->getNamespaces())) {
			return TRUE;
		}
		return FALSE;
	}

}
