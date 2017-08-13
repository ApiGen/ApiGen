<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\AutocompleteElements;
use ApiGen\Templating\TemplateRenderer;

final class AutoCompleteDataGenerator implements GeneratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    public function __construct(
        Configuration $configuration,
        TemplateRenderer $templateRenderer,
        AutocompleteElements $autocompleteElements
    ) {
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
        $this->autocompleteElements = $autocompleteElements;
    }

    public function generate(): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplatesDirectory() . DIRECTORY_SEPARATOR . 'elementlist.js.latte',
            $this->configuration->getDestination() . DIRECTORY_SEPARATOR . 'elementlist.js',
            [
                'autocompleteElements' => $this->autocompleteElements->getElements(),
            ]
        );
    }
}
