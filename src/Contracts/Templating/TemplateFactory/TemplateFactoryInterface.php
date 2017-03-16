<?php declare(strict_types=1);

namespace ApiGen\Contracts\Templating\TemplateFactory;

use ApiGen\Templating\Template;

interface TemplateFactoryInterface
{

    public function create(): Template;
}
