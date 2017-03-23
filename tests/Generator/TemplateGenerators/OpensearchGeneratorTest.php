<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\OpensearchGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

class OpensearchGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var OpensearchGenerator
     */
    private $opensearchGenerator;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
        $this->opensearchGenerator = $this->container->getByType(OpensearchGenerator::class);
    }

    public function testIsAllowed(): void
    {
        $this->configuration->resolveOptions([
            'destination' => TEMP_DIR . '/api',
            'source' => TEMP_DIR,
            'googleCseId' => null,
            'baseUrl' => null
        ]);
        $this->assertFalse($this->opensearchGenerator->isAllowed());
        $this->setCorrectConfiguration();
        $this->assertTrue($this->opensearchGenerator->isAllowed());
    }

    public function testGenerate(): void
    {
        $this->setCorrectConfiguration();
        $this->opensearchGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/api/opensearch.xml');
    }

    private function setCorrectConfiguration(): void
    {
        $this->configuration->resolveOptions([
            'destination' => TEMP_DIR . '/api',
            'source' => TEMP_DIR,
            'googleCseId' => '12345',
            'baseUrl' => 'http://apigen.org'
        ]);
    }
}
