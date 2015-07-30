<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Theme\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;

interface ThemeConfigurationInterface
{

    /**
     * @var string
     */
    const ORDER_ALPHABETIC = 'alphabetic';

    /**
     * @var string
     */
    const ORDER_NATURAL = 'natural';


    /**
     * Get theme name.
     *
     * @return string
     */
    public function getName();


    /**
     * @return bool
     */
    public function shouldElementDetailsCollapse();


    /**
     * All css/js/images resources
     *
     * @return array {[ absoluteSource => relativeDestination ]}
     */
    public function getResources();


    /**
     * Absolute path to directory with templates.
     *
     * @return string
     */
    public function getTemplatesPath();


    /**
     * Order of elements (possible values: self::ORDER_ALPHABETIC, self::ORDER_NATURAL)
     *
     * @return string
     */
    public function getElementsOrder();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getOverviewTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getOverviewDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
        * @return string
     */
    public function getCombinedTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getCombinedDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getElementListTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getElementListDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getE404Template();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getE404Destination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getPackageTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getPackageDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getNamespaceTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getNamespaceDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getClassTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getClassDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getConstantTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getConstantDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getFunctionTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getFunctionDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getAnnotationGroupTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getAnnotationGroupDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getSourceTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getSourceDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getTreeTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getTreeDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getSitemapTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getSitemapDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getOpensearchTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    public function getOpensearchDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    public function getRobotsTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     * @return string
     */
    public function getRobotsDestination();
}
