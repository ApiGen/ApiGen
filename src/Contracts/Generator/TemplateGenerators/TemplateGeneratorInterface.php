<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\TemplateGenerators;

interface TemplateGeneratorInterface
{

    /**
     * Generate template to file
     */
    public function generate(): void;
}
