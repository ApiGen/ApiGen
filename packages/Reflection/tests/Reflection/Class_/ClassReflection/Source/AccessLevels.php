<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source;

final class AccessLevels extends ParentClass implements RichInterface
{
    use SomeTrait;

    /**
     * @var int
     */
    public const LEVEL = 5;

    /**
     * @var mixed
     */
    public $publicProperty;

    /**
     * @var mixed
     */
    protected $protectedProperty;

    /**
     * @var mixed
     */
    private $privateProperty;

    public function publicMethod(): void
    {
    }

    public function getSomeStuff(): void
    {
    }

    protected function protectedMethod(): void
    {
    }

    private function privateMethod(): void
    {
    }
}
