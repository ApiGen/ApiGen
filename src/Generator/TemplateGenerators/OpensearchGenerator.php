<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Templating\TemplateFactory;

class OpensearchGenerator implements ConditionalTemplateGeneratorInterface
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;


    public function __construct(Configuration $configuration, TemplateFactory $templateFactory)
    {
        $this->configuration = $configuration;
        $this->templateFactory = $templateFactory;
    }


    public function generate(): void
    {
        $this->templateFactory->createForType('opensearch')
            ->save();
    }


    public function isAllowed(): bool
    {
        $options = $this->configuration->getOptions();
        return $options[ConfigurationOptions::GOOGLE_CSE_ID] && $options[ConfigurationOptions::BASE_URL];
    }
}
