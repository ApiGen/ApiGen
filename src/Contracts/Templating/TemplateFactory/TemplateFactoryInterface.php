<?php

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Contracts\Templating\Template\TemplateInterface;

interface TemplateFactoryInterface
{

    /**
     * @return TemplateInterface
     */
    public function create();
}
