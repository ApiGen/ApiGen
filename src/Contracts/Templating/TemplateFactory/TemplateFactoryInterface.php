<?php

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Templating\Template;

interface TemplateFactoryInterface
{

    /**
     * @return Template
     */
    public function create();
}
