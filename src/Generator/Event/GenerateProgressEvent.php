<?php

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
