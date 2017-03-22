<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
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


    public function __construct(ConfigurationInterface $configuration, ParserStorageInterface $parserStorage)
    {
        $this->configuration = $configuration;
        $this->parserStorage = $parserStorage;
    }


    /**
     * @param IReflectionClass|IReflectionConstant|IReflectionMethod $tokenReflection
     * @return ReflectionClass|ReflectionConstant|ReflectionMethod
     */
    public function createFromReflection($tokenReflection)
    {
        $reflection = $this->createByReflectionType($tokenReflection);
        $this->setDependencies($reflection);
        return $reflection;
    }


    /**
     * @param mixed[] $settings
     * @return MagicMethodReflectionInterface
     */
    public function createMethodMagic(array $settings): MagicMethodReflectionInterface
    {
        $reflection = new ReflectionMethodMagic($settings);
        $this->setDependencies($reflection);

        return $reflection;
    }


    /**
     * @param mixed[] $settings
     */
    public function createParameterMagic(array $settings): MagicParameterReflectionInterface
    {
        $reflection = new ReflectionParameterMagic($settings);
        $this->setDependencies($reflection);

        return $reflection;
    }


    /**
     * @param mixed[] $settings
     */
    public function createPropertyMagic(array $settings): MagicPropertyReflectionInterface
    {
        $reflection = new ReflectionPropertyMagic($settings);
        $this->setDependencies($reflection);

        return $reflection;
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


    private function setDependencies(ReflectionBase $reflection): void
    {
        $reflection->setConfiguration($this->configuration);
        $reflection->setParserStorage($this->parserStorage);
        $reflection->setReflectionFactory($this);
    }
}
