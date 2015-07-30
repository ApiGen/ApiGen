<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Elements;

interface ElementsInterface
{

    const CLASSES = 'classes';
    const CONSTANTS = 'constants';
    const EXCEPTIONS = 'exceptions';
    const FUNCTIONS = 'functions';
    const INTERFACES = 'interfaces';
    const TRAITS = 'traits';
    const PROPERTIES = 'properties';
    const METHODS = 'methods';


    /**
     * @return string[]
     */
    public function getClassTypeList();


    /**
     * @return string[]
     */
    public function getAll();


    /**
     * @return array[]
     */
    public function getEmptyList();
}
