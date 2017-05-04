<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;

final class ElementFilter implements ElementFilterInterface
{
    /**
     * @param ReflectionInterface[] $elements
     * @return ReflectionInterface[]
     */
    public function filterByAnnotation(array $elements, string $annotation): array
    {
        return array_filter($elements, function (ReflectionInterface $element) use ($annotation) {
            return $element->hasAnnotation($annotation);
        });
    }
}
