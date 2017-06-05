<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Templating\TemplateRenderer;

final class NamespacesGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    private const NAME = 'namespaces';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;
    /**
     * @var NamespaceStorage
     */
    private $namespaceStorage;

    public function __construct(
        NamespaceStorage $namespaceStorage,
        Configuration $configuration,
        TemplateRenderer $templateRenderer
    ) {
        $this->namespaceStorage = $namespaceStorage;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName(self::NAME),
            $this->configuration->getDestinationWithName(self::NAME),
            [
                'activePage' => self::NAME,
                'title' => ucfirst(self::NAME),
                self::NAME => $this->namespaceStorage->getNamespaces()
            ]
        );
    }
}
