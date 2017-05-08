<?php declare(strict_types=1);

namespace ApiGen\Element\Tree;

use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class ImplementersResolver
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function setReflectionStorage(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
    }

    public function resolve($arg)
    {
        dump($arg);
        die;
    }
}
