<?php

namespace ApiGen\Contracts\Generator\TemplateGenerators;

interface ConditionalTemplateGeneratorInterface extends TemplateGeneratorInterface
{

    /**
     * @return bool
     */
    public function isAllowed();
}
