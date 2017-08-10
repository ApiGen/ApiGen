<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Templating\TemplateRenderer;

final class IndexGenerator implements GeneratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    public function __construct(Configuration $configuration, TemplateRenderer $templateRenderer)
    {
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('index'),
            $this->configuration->getDestinationWithName('index'),
            [
                'activePage' => 'overview',
            ]
        );
    }
}
