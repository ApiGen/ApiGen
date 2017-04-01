<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Exception\Parser\Reflection\UnsupportedClassException;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\Parser\Reflection\ReflectionParameter;
use ApiGen\Parser\Reflection\ReflectionProperty;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;
use TokenReflection\IReflectionProperty;

/**
 * @todo rename to TransformerCollector
 * @todo decouple and add TranformerInterface per item
 */
final class ReflectionFactory implements ReflectionFactoryInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(
        ConfigurationInterface $configuration,
        ParserStorageInterface $parserStorage,
        TransformerCollectorInterface $transformerCollector
    ) {
        $this->configuration = $configuration;
        $this->parserStorage = $parserStorage;
        $this->transformerCollector = $transformerCollector;
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
     * @param IReflectionClass|IReflectionConstant|IReflectionMethod $reflection
     * @return mixed
     */
    private function createByReflectionType($reflection)
    {
        foreach ($this->transformerCollector->getTransformers() as $transformer) {
            if ( ! $transformer->matches($reflection)) {
                continue;
            }

            return $transformer->transform($reflection);
        }

        if ($reflection instanceof IReflectionConstant) {
            return new ReflectionConstant($reflection);
        }

        if ($reflection instanceof IReflectionMethod) {
            return new ReflectionMethod($reflection);
        }

        if ($reflection instanceof IReflectionProperty) {
            return new ReflectionProperty($reflection);
        }

        if ($reflection instanceof IReflectionParameter) {
            return new ReflectionParameter($reflection);
        }

        if ($reflection instanceof IReflectionFunction) {
            return new ReflectionFunction($reflection);
        }

        throw new UnsupportedClassException(sprintf(
            'Invalid reflection class "%s". Register new transformer.',
            get_class($reflection)
        ));
    }

    private function setDependencies(AbstractReflection $reflection): void
    {
        $reflection->setConfiguration($this->configuration);
        $reflection->setParserStorage($this->parserStorage);
        $reflection->setReflectionFactory($this);
    }
}
