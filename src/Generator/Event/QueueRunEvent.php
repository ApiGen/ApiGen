<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Event;

use ApiGen\Contracts\EventDispatcher\Event\EventInterface;

class QueueRunEvent implements EventInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $stepCount;


    /**
     * @param string $name
     * @param int $stepCount
     */
    public function __construct($name, $stepCount)
    {
        $this->name = $name;
        $this->stepCount = $stepCount;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return int
     */
    public function getStepCount()
    {
        return $this->stepCount;
    }
}
