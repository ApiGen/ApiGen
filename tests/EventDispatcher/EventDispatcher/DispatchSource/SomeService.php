<?php

namespace ApiGen\EventDispatcher\Tests\EventDispatcher\DispatchSource;

class SomeService
{

    /**
     * @var string
     */
    private $value = 5;


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
