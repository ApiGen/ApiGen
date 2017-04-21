<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Templating\TemplateFactory;

final class IndexGenerator implements GeneratorInterface
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(TemplateFactory $templateFactory, ConfigurationInterface $configuration)
    {
        $this->templateFactory = $templateFactory;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->configuration->getTemplateByName('index'));
        $template->save($this->configuration->getDestinationWithName('index'));
    }
}
