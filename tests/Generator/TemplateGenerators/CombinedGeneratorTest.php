<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\CombinedGenerator;
use ApiGen\Tests\ContainerAwareTestCase;

class CombinedGeneratorTest extends ContainerAwareTestCase
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var CombinedGenerator
     */
    private $combinedGenerator;


    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
        $this->combinedGenerator = $this->container->getByType(CombinedGenerator::class);
    }


    public function testGenerate(): void
    {
        $this->configuration->resolveOptions([
            'source' => TEMP_DIR,
            'destination' => TEMP_DIR . '/api'
        ]);
        $this->combinedGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/api/resources/combined.js');
    }
}
