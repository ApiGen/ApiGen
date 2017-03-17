<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;

final class ElementFilter implements ElementFilterInterface
{
    /**
     * @param ElementReflectionInterface[] $elements
     * @return ElementReflectionInterface[]
     */
    public function filterForMain(array $elements): array
    {
        return array_filter($elements, function (ElementReflectionInterface $element) {
            return $element->isMain();
        });
    }

    /**
     * @param ElementReflectionInterface[] $elements
     * @return ElementReflectionInterface[]
     */
    public function filterByAnnotation(array $elements, string $annotation): array
    {
        return array_filter($elements, function (ElementReflectionInterface $element) use ($annotation) {
            return $element->hasAnnotation($annotation);
        });
    }
}
