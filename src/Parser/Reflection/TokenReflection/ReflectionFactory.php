<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\ReflectionBase;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionExtension;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\Parser\Reflection\ReflectionMethodMagic;
use ApiGen\Parser\Reflection\ReflectionParameter;
use ApiGen\Parser\Reflection\ReflectionParameterMagic;
use ApiGen\Parser\Reflection\ReflectionProperty;
use ApiGen\Parser\Reflection\ReflectionPropertyMagic;
use RuntimeException;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionExtension;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;
use TokenReflection\IReflectionProperty;

class ReflectionFactory implements ReflectionFactoryInterface
{

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;


    public function __construct(ConfigurationInterface $configuration, ParserStorageInterface $parserResult)
    {
        $this->configuration = $configuration;
        $this->parserStorage = $parserResult;
    }


    /**
     * {@inheritdoc}
     */
    public function createFromReflection($tokenReflection)
    {
        $reflection = $this->createByReflectionType($tokenReflection);
        return $this->setDependencies($reflection);
    }


    /**
     * {@inheritdoc}
     */
    public function createMethodMagic(array $settings)
    {
        $reflection = new ReflectionMethodMagic($settings);
        return $this->setDependencies($reflection);
    }


    /**
     * {@inheritdoc}
     */
    public function createParameterMagic(array $settings)
    {
        $reflection = new ReflectionParameterMagic($settings);
        return $this->setDependencies($reflection);
    }


    /**
     * {@inheritdoc}
     */
    public function createPropertyMagic(array $settings)
    {
        $reflection = new ReflectionPropertyMagic($settings);
        return $this->setDependencies($reflection);
    }


    /**
     * @param IReflectionClass|IReflectionConstant|IReflectionMethod $reflection
     * @return ReflectionClass|ReflectionConstant|ReflectionMethod
     */
    private function createByReflectionType($reflection)
    {
        if ($reflection instanceof IReflectionClass) {
            return new ReflectionClass($reflection);

        } elseif ($reflection instanceof IReflectionConstant) {
            return new ReflectionConstant($reflection);

        } elseif ($reflection instanceof IReflectionMethod) {
            return new ReflectionMethod($reflection);

        } elseif ($reflection instanceof IReflectionProperty) {
            return new ReflectionProperty($reflection);

        } elseif ($reflection instanceof IReflectionParameter) {
            return new ReflectionParameter($reflection);

        } elseif ($reflection instanceof IReflectionFunction) {
            return new ReflectionFunction($reflection);

        } elseif ($reflection instanceof IReflectionExtension) {
            return new ReflectionExtension($reflection);
        }

        throw new RuntimeException('Invalid reflection class type ' . get_class($reflection));
    }


    /**
     * @return ReflectionBase
     */
    private function setDependencies(ReflectionBase $reflection)
    {
        $reflection->setConfiguration($this->configuration);
        $reflection->setParserResult($this->parserStorage);
        $reflection->setReflectionFactory($this);
        return $reflection;
    }
}
