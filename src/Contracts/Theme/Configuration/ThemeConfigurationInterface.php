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
    function getName();


    /**
     * @return bool
     */
    function shouldElementDetailsCollapse();


    /**
     * All css/js/images resources
     *
     * @return array {[ absoluteSource => relativeDestination ]}
     */
    function getResources();


    /**
     * Absolute path to directory with templates.
     *
     * @return string
     */
    function getTemplatesPath();


    /**
     * Order of elements (possible values: self::ORDER_ALPHABETIC, self::ORDER_NATURAL)
     *
     * @return string
     */
    function getElementsOrder();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getOverviewTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getOverviewDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
        * @return string
     */
    function getCombinedTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getCombinedDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getElementListTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getElementListDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getE404Template();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getE404Destination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getPackageTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getPackageDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getNamespaceTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getNamespaceDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getClassTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getClassDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getConstantTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getConstantDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getFunctionTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getFunctionDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getAnnotationGroupTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getAnnotationGroupDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getSourceTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getSourceDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getTreeTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getTreeDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getSitemapTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getSitemapDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getOpensearchTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     *
     * @return string
     */
    function getOpensearchDestination();


    /**
     * Absolute path to template file (*.latte, *.twig).
     *
     * @return string
     */
    function getRobotsTemplate();


    /**
     * Relative path to destination file, that can be opened in browser (*.html).
     * @return string
     */
    function getRobotsDestination();
}
