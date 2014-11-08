<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Elements\ElementStorage;
use ApiGen\Elements\PackageElement;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Templating\TemplateNavigator;
use Nette;


class PackagesTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;


	public function __construct(
		TemplateNavigator $templateNavigator,
		TemplateFactory $templateFactory,
		ElementStorage $elementStorage
	) {
		$this->templateNavigator = $templateNavigator;
		$this->templateFactory = $templateFactory;
		$this->elementStorage = $elementStorage;
	}


	public function generate()
	{
		$template = $this->templateFactory->create('package');
		foreach ($this->elementStorage->getPackages() as $name => $package) {
			$this->generateForPackage($template, $name, $package);
		}
	}


	/**
	 * @param Template|\stdClass $template
	 * @param string $name
	 * @param array $package
	 */
	private function generateForPackage(Template $template, $name, $package)
	{
		$template->package = $name;
		$template->subpackages = $this->getSubpackagesForPackage($template->packages, $name);
		$template->classes = $package['classes'];
		$template->interfaces = $package['interfaces'];
		$template->traits = $package['traits'];
		$template->exceptions = $package['exceptions'];
		$template->constants = $package['constants'];
		$template->functions = $package['functions'];

		$savePath = $this->templateNavigator->getTemplatePathForPackage($name);
		$template->setSavePath($savePath);
		$template->save();
	}


	/**
	 * @param array $packages
	 * @param string $packageName
	 * @return array
	 */
	private function getSubpackagesForPackage($packages, $packageName)
	{
		array_filter($packages, function ($subpackageName) use ($packageName) {
			return (bool) preg_match('~^' . preg_quote($packageName) . '\\\\[^\\\\]+$~', $subpackageName);
		});
		return $packages;
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		if (count($this->elementStorage->getPackages())) {
			return TRUE;
		}
		return FALSE;
	}

}
