<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;

final class ElementListGenerator implements GeneratorInterface
{
    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(TemplateFactoryInterface $templateFactory, ConfigurationInterface $configuration)
    {
        $this->templateFactory = $templateFactory;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());
        $template->save($this->getDestinationPath());
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplatesDirectory()
            . DIRECTORY_SEPARATOR
            . 'elementlist.js.latte';
    }

    private function getDestinationPath(): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . 'elementlist.js';
    }
}
