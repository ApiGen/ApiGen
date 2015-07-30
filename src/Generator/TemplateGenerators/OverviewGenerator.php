<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Templating\TemplateFactory;

class OverviewGenerator implements TemplateGeneratorInterface
{

    /**
     * @var TemplateFactory
     */
    private $templateFactory;


    public function __construct(TemplateFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->templateFactory->createForType(TCO::OVERVIEW)
            ->save();
    }
}
