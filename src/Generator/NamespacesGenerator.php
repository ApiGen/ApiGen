<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
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
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    public function __construct(
        NamespaceReflectionCollector $namespaceReflectionCollector,
        Configuration $configuration,
        TemplateRenderer $templateRenderer
    ) {
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        if (count($this->namespaceReflectionCollector->getNamespaces()) < 1) {
            return;
        }

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName(self::NAME),
            $this->configuration->getDestinationWithName(self::NAME),
            [
                'activePage' => self::NAME,
                'pageTitle' => ucfirst(self::NAME),
                self::NAME => $this->namespaceReflectionCollector->getNamespaces()
            ]
        );
    }
}
