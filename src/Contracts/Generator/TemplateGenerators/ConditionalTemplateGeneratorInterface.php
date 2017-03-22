<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\TemplateGenerators;

interface ConditionalTemplateGeneratorInterface extends TemplateGeneratorInterface
{
    public function isAllowed(): bool;
}
