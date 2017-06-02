<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Templating\TemplateRenderer;

final class ElementListGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    public function __construct(ConfigurationInterface $configuration, TemplateRenderer $templateRenderer)
    {
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplatesDirectory() . DIRECTORY_SEPARATOR . 'elementlist.js.latte',
            $this->configuration->getDestination() . DIRECTORY_SEPARATOR . 'elementlist.js'
        );
    }
}
