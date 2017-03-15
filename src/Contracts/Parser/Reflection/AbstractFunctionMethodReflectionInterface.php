<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

interface AbstractFunctionMethodReflectionInterface extends ElementReflectionInterface
{

    /**
     * @return bool
     */
    public function returnsReference();


    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters();


    /**
     * @param int|string $key
     * @return ParameterReflectionInterface
     */
    public function getParameter($key);
}
