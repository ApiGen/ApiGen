<?php

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Contracts\Templating\Template\TemplateInterface;

interface TemplateDecoratorInterface
{

    /**
     * @return TemplateInterface
     */
    public function decorate(TemplateInterface $template);
}
