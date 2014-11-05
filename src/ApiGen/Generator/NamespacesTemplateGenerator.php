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
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;


	public function __construct(
		TemplateNavigator $templateNavigator,
		ElementStorage $elementStorage,
		TemplateFactory $templateFactory
	) {
		$this->templateNavigator = $templateNavigator;
		$this->elementStorage = $elementStorage;
		$this->templateFactory = $templateFactory;
	}


	public function generate()
	{
		$template = $this->templateFactory->create('namespace');
		foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
			$this->generateForNamespace($template, $name, $namespace);
		}
	}


	/**
	 * @param Template|\stdClass $template
	 * @param string $name
	 * @param array $namespace
	 */
	private function generateForNamespace(Template $template, $name, $namespace)
	{
		$template->namespace = $name;
		$template->subnamespaces = $this->getSubnamespacesForNamespace($template->namespaces, $name);

		$template->classes = $namespace['classes'];
		$template->interfaces = $namespace['interfaces'];
		$template->traits = $namespace['traits'];
		$template->exceptions = $namespace['exceptions'];
		$template->constants = $namespace['constants'];
		$template->functions = $namespace['functions'];

		$savePath = $this->templateNavigator->getTemplatePathForNamespace($name);
		$template->setSavePath($savePath);
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
