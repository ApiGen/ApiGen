<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Element\Namespaces\NamespaceStorage;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\Templating\TemplateRenderer;

final class ClassesGenerator implements GeneratorInterface
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
     * @var NamespaceStorage
     */
    private $namespaceStorage;
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(
        ReflectionStorageInterface $reflectionStorage,
        Configuration $configuration,
        TemplateRenderer $templateRenderer
    ) {
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
        $this->reflectionStorage = $reflectionStorage;
    }

    public function generate(): void
    {
        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('classes'),
            $this->configuration->getDestinationWithName('classes'),
            [
                'classes' => $this->reflectionStorage->getClassReflections()
            ]
        );
    }
}
