<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer;

use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use ApiGen\ReflectionToElementTransformer\Exception\UnsupportedReflectionClassException;

final class TransformerCollector implements TransformerCollectorInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers = [];

    public function addTransformer(TransformerInterface $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    /**
     * @param object $reflection
     * @return mixed
     */
    public function transformReflectionToElement($reflection)
    {
        foreach ($this->transformers as $transformer) {
            if (! $transformer->matches($reflection)) {
                continue;
            }

            return $transformer->transform($reflection);
        }

        throw new UnsupportedReflectionClassException(sprintf(
            'Reflection class "%s" is not yet supported. Register new transformer implementing "%s".',
            get_class($reflection),
            TransformerInterface::class
        ));
    }
}
