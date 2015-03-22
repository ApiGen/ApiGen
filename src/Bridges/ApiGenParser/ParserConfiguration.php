<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Bridges\ApiGenParser;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Configuration\ParserConfigurationInterface;


class ParserConfiguration implements ParserConfigurationInterface
{

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getVisibilityLevel()
	{
		return $this->configuration->getOption(CO::VISIBILITY_LEVELS);
	}


	/**
	 * {@inheritdoc}
	 */
	public function getMain()
	{
		return $this->configuration->getOption(CO::MAIN);
	}


	/**
	 * {@inheritdoc}
	 */
	public function isPhpCoreDocumented()
	{
		return $this->configuration->getOption(CO::PHP);
	}


	/**
	 * {@inheritdoc}
	 */
	public function isInternalDocumented()
	{
		return $this->configuration->getOption(CO::INTERNAL);
	}


	/**
	 * {@inheritdoc}
	 */
	public function isDeprecatedDocumented()
	{
		return $this->configuration->getOption(CO::DEPRECATED);
	}


	/**
	 * {@inheritdoc}
	 */
	public function areNamespacesEnabled($namespaceCount, $packageCount)
	{
		return $this->configuration->areNamespacesEnabled($namespaceCount, $packageCount);
	}


	/**
	 * {@inheritdoc}
	 */
	public function arePackagesEnabled($areNamespaceEnabled)
	{
		return $this->configuration->arePackagesEnabled($areNamespaceEnabled);
	}

}
