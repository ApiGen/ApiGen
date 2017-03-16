<?php declare(strict_types=1);

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


    public function generate(): void
    {
        $this->templateFactory->createForType(TCO::OVERVIEW)
            ->save();
    }
}
