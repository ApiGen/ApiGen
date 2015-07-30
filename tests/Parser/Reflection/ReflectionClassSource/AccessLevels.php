<?php

namespace Project;

/**
 * @property $someMagicProperty
 * @method getSome()
 */
class AccessLevels extends ParentClass implements RichInterface
{

    use SomeTrait;
    use SomeTraitNotPresentHere;

    const LEVEL = 5;

    public $publicProperty;

    protected $protectedProperty;

    private $privateProperty;


    public function publicMethod()
    {
    }


    protected function protectedMethod()
    {
    }


    private function privateMethod()
    {
    }
}
