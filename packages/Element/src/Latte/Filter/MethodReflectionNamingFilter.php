<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Element\Naming\ReflectionNaming;

final class MethodReflectionNamingFilter // implements FilterProviderInterface
{
    /**
     * @var ReflectionNaming
     */
    private $reflectionNaming;

    public function __construct(ReflectionNaming $reflectionNaming)
    {
        $this->reflectionNaming = $reflectionNaming;
    }

    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            'prettyMethodName' => function (MethodReflectionInterface $methodReflection) {
                return $this->reflectionNaming->forMethodReflection($methodReflection);
            }
        ];
    }
}
