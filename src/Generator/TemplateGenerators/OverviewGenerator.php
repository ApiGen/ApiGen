<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Templating\TemplateFactory;

final class OverviewGenerator implements TemplateGeneratorInterface
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        TemplateFactory $templateFactory,
        ConfigurationInterface $configuration
    ) {
        $this->templateFactory = $templateFactory;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());
        $template->save($this->createFileDestination());
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplatesDirectory()
            . DIRECTORY_SEPARATOR
            . 'overview.latte';
    }

    private function createFileDestination(): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . 'index.html';
    }
}
