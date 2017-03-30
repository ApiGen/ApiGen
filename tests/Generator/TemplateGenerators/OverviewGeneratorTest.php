<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class OverviewGeneratorTest extends AbstractContainerAwareTestCase
{
    protected function setUp(): void
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
            'source' => [__DIR__],
            'destination' => TEMP_DIR
        ]);
    }

    public function testGenerate(): void
    {
        /** @var TemplateFactoryInterface $templateFactory */
        $templateFactory = $this->container->getByType(TemplateFactoryInterface::class);

        $overviewGenerator = new OverviewGenerator($templateFactory);
        $overviewGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/index.html');
    }
}
