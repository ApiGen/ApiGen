<?php declare(strict_types=1);

namespace Project;

/**
 * @property $someMagicProperty
 * @property \stdClass[] $issue696
 * @method getSome()
 * @method \stdClass[] methodRelatedToIssue696(\stdClass[] $argument)
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
