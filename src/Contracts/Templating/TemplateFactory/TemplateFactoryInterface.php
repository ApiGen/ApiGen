<?php declare(strict_types=1);

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Templating\Template;

interface TemplateFactoryInterface
{
    public function create(): Template;

    public function createForType(string $type): Template;

    /**
     * @param string $name
     * @param ElementReflectionInterface|string $element
     */
    public function createNamedForElement(string $name, $element): Template;

    public function createForReflection(ElementReflectionInterface $element): Template;
}
