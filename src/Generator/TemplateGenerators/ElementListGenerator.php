<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;

final class ElementListGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(ConfigurationInterface $configuration, TemplateRendererInterface $templateRenderer)
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
