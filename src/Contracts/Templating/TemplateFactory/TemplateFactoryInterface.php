<?php declare(strict_types=1);

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Templating\Template;

interface TemplateFactoryInterface
{
    public function create(): Template;

    public function createForType(string $type): Template;

    public function createForReflection(ElementReflectionInterface $element): Template;
}
