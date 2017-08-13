<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source;

class ParentClass
{
    /**
     * @var int
     */
    public const SOME_PARENT_CONSTANT = 123;

    /**
     * @var mixed
     */
    protected $someParentProperty;

    /**
     * @return mixed
     */
    public function getSomeParentStuff()
    {
        return $this->someParentProperty;
    }
}
