<?php

namespace ApiGen\Contracts\Configuration;

interface ConfigurationInterface
{

    /**
     * @return array
     */
    public function resolveOptions(array $options);


    /**
     * @param string $name
     * @return mixed|NULL
     */
    public function getOption($name);


    /**
     * @return array
     */
    public function getOptions();


    public function setOptions(array $options);


    /**
     * Get property/method visibility level (public, protected or private, in binary code).
     *
     * @return int
     */
    public function getVisibilityLevel();


    /**
     * Return name of main library
     *
     * @return string
     */
    public function getMain();


    /**
     * Are PHP Core elements documented.
     *
     * @return bool
     */
    public function isPhpCoreDocumented();


    /**
     * Are elements marked as "@internal" documented.
     *
     * @return bool
     */
    public function isInternalDocumented();


    /**
     * Are elements marked as "@deprecated" documented.
     *
     * @return bool
     */
    public function isDeprecatedDocumented();


    /**
     * Is grouping by namespaces enabled.
     *
     * @return bool
     */
    public function areNamespacesEnabled();


    /**
     * Is grouping by packages enabled.
     *
     * @return bool
     */
    public function arePackagesEnabled();


    /**
     * List of annotations.
     *
     * @return string[]
     */
    public function getAnnotationGroups();


    /**
     * @return bool
     */
    public function isTreeAllowed();


    /**
     * @return string
     */
    public function getDestination();


    /**
     * Get title of the project.
     *
     * @return string
     */
    public function getTitle();


    /**
     * Base url of application.
     *
     * @var string
     */
    public function getBaseUrl();


    /**
     * @return string
     */
    public function getGoogleCseId();


    /**
     * @return bool
     */
    public function shouldGenerateSourceCode();


    /**
     * @return string[]
     */
    public function getSource();


    /**
     * Exclude masks for files/directories.
     *
     * @return string[]
     */
    public function getExclude();


    /**
     * File extensions to be taken in account.
     *
     * @return string[]
     */
    public function getExtensions();
}
