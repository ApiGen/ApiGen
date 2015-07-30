<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use Nette\Utils\Strings;

class Configuration implements ConfigurationInterface
{

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var ConfigurationOptionsResolver
     */
    private $configurationOptionsResolver;


    public function __construct(ConfigurationOptionsResolver $configurationOptionsResolver)
    {
        $this->configurationOptionsResolver = $configurationOptionsResolver;
    }


    /**
     * {@inheritdoc}
     */
    public function resolveOptions(array $options)
    {
        $options = $this->unsetConsoleOptions($options);
        $this->options = $options = $this->configurationOptionsResolver->resolve($options);
        return $options;
    }


    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        if (isset($this->getOptions()[$name])) {
            return $this->getOptions()[$name];
        }
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if ($this->options === null) {
            $this->resolveOptions([]);
        }
        return $this->options;
    }


    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }


    /**
     * {@inheritdoc}
     */
    public function areNamespacesEnabled()
    {
        return $this->getOption('groups') === 'namespaces';
    }


    /**
     * {@inheritdoc}
     */
    public function arePackagesEnabled()
    {
        return $this->getOption('groups') === 'packages';
    }


    /**
     * {@inheritdoc}
     */
    public function getZipFileName()
    {
        $webalizedTitle = Strings::webalize($this->getOption('title'), null, false);
        return ($webalizedTitle ? '-' : '') . 'API-documentation.zip';
    }


    /**
     * {@inheritdoc}
     */
    public function getVisibilityLevel()
    {
        return $this->options['visibilityLevels'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMain()
    {
        return $this->getOption('main');
    }


    /**
     * {@inheritdoc}
     */
    public function isPhpCoreDocumented()
    {
        return (bool) $this->getOption('php');
    }


    /**
     * {@inheritdoc}
     */
    public function isInternalDocumented()
    {
        return (bool) $this->getOption('internal');
    }


    /**
     * {@inheritdoc}
     */
    public function isDeprecatedDocumented()
    {
        return (bool) $this->getOption('deprecated');
    }


    /**
     * {@inheritdoc}
     */
    public function getAnnotationGroups()
    {
        return $this->options['annotationGroups'];
    }


    /**
     * {@inheritdoc}
     */
    public function isAvailableForDownload()
    {
        return $this->options['download'];
    }


    /**
     * {@inheritdoc}
     */
    public function isTreeAllowed()
    {
        return $this->options['tree'];
    }


    /**
     * {@inheritdoc}
     */
    public function getDestination()
    {
        return $this->options['destination'];
    }


    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->options['title'];
    }


    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        return $this->options['baseUrl'];
    }


    /**
     * {@inheritdoc}
     */
    public function getGoogleCseId()
    {
        return $this->options['googleCseId'];
    }


    /**
     * {@inheritdoc}
     */
    public function shouldGenerateSourceCode()
    {
        return $this->options['sourceCode'];
    }


    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->options['source'];
    }


    /**
     * {@inheritdoc}
     */
    public function getExclude()
    {
        return $this->options['exclude'];
    }


    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return $this->options['extensions'];
    }


    /**
     * @return array
     */
    private function unsetConsoleOptions(array $options)
    {
        unset($options['config'], $options['help'], $options['version'], $options['quiet']);
        return $options;
    }
}
