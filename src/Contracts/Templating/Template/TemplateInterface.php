<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Templating\Template;

interface TemplateInterface
{

    /**
     * Save file to targed location.
     *
     * @param string $file
     */
    public function save($file);


    /**
     * Set template file.
     *
     * @param string $file
     */
    public function setFile($file);


    /**
     * Get used parameters.
     *
     * @return array
     */
    public function getParameters();


    /**
     * Set parameters.
     */
    public function setParameters(array $parameters);
}
