<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;

class ElementFilter implements ElementFilterInterface
{

    public function filterForMain(array $elements)
    {
        return array_filter($elements, function (ElementReflectionInterface $element) {
            return $element->isMain();
        });
    }


    public function filterByAnnotation(array $elements, string $annotation): array
    {
        return array_filter($elements, function (ElementReflectionInterface $element) use ($annotation) {
            return $element->hasAnnotation($annotation);
        });
    }
}
