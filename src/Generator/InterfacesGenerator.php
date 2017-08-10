<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Reflection\ReflectionStorage;
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
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        Configuration $configuration,
        TemplateRenderer $templateRenderer
    ) {
        $this->configuration = $configuration;
        $this->templateRenderer = $templateRenderer;
        $this->reflectionStorage = $reflectionStorage;
    }

    public function generate(): void
    {
        if (count($this->reflectionStorage->getInterfaceReflections()) < 1) {
            return;
        }

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName(self::NAME),
            $this->configuration->getDestinationWithName(self::NAME),
            [
                'activePage' => self::NAME,
                'pageTitle' => ucfirst(self::NAME),
                self::NAME => $this->reflectionStorage->getInterfaceReflections(),
            ]
        );
    }
}
