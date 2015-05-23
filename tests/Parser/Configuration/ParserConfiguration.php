<?php

namespace ApiGen\Parser\Tests\Configuration;

use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;
use ReflectionProperty;


class ParserConfiguration implements ParserConfigurationInterface
{

	/**
	 * @var int
	 */
	private $visibilityLevel;

	/**
	 * @var string
	 */
	private $main;

	/**
	 * @var bool
	 */
	private $isPhpCoreDocumented;

	/**
	 * @var bool
	 */
	private $isInternalDocumented;

	/**
	 * @var bool
	 */
	private $isDeprecatedDocumented;


	/**
	 * @param int $visibilityLevel
	 * @param string $main
	 * @param bool $isPhpCoreDocumented
	 * @param bool $isInternalDocumented
	 * @param bool $isDeprecatedDocumented
	 */
	public function __construct(
		$visibilityLevel = ReflectionProperty::IS_PUBLIC,
		$main = '',
		$isPhpCoreDocumented = FALSE,
		$isInternalDocumented = FALSE,
		$isDeprecatedDocumented = FALSE
	) {
		$this->visibilityLevel = $visibilityLevel;
		$this->main = $main;
		$this->isPhpCoreDocumented = $isPhpCoreDocumented;
		$this->isInternalDocumented = $isInternalDocumented;
		$this->isDeprecatedDocumented = $isDeprecatedDocumented;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getVisibilityLevel()
	{
		return $this->visibilityLevel;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getMain()
	{
		return $this->main;
	}


	/**
	 * {@inheritdoc}
	 */
	public function isPhpCoreDocumented()
	{
		return $this->isPhpCoreDocumented;
	}


	/**
	 * {@inheritdoc}
	 */
	public function isInternalDocumented()
	{
		return $this->isInternalDocumented;
	}


	/**
	 * {@inheritdoc}
	 */
	public function isDeprecatedDocumented()
	{
		return $this->isDeprecatedDocumented;
	}


	/**
	 * {@inheritdoc}
	 */
	public function areNamespacesEnabled()
	{
		return TRUE;
	}


	/**
	 * {@inheritdoc}
	 */
	public function arePackagesEnabled()
	{
		return FALSE;
	}

}
