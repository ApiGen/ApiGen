<?php

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
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


    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $this->templateFactory->createForType('opensearch')
            ->save();
    }


    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        $options = $this->configuration->getOptions();
        return $options[CO::GOOGLE_CSE_ID] && $options[CO::BASE_URL];
    }
}
