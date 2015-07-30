<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Event;

use ApiGen\Contracts\EventDispatcher\Event\EventInterface;

class GenerateProgressEvent implements EventInterface
{

    /**
     * @var string
     */
    private $name;


    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
