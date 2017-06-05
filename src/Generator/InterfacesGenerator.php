<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\Templating\TemplateRenderer;

final class InterfacesGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    private const NAME = 'interfaces';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

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
            $this->configuration->getTemplateByName(self::NAME),
            $this->configuration->getDestinationWithName(self::NAME),
            [
                self::NAME => $this->reflectionStorage->getInterfaceReflections()
            ]
        );
    }
}