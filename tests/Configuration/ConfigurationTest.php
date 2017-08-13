<?php declare(strict_types=1);

namespace ApiGen\Tests\Configuration;

use ApiGen\Configuration\Configuration;
use ApiGen\ModularConfiguration\Exception\ConfigurationException;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\OverwriteOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\ModularConfiguration\Option\ThemeDirectoryOption;
use ApiGen\ModularConfiguration\Option\TitleOption;
use ApiGen\ModularConfiguration\Option\VisibilityLevelOption;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ConfigurationTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
    }

    public function testResolve(): void
    {
        $options = $this->configuration->resolveOptions([
            SourceOption::NAME => [],
            DestinationOption::NAME => TEMP_DIR,
        ]);

        $this->assertCount(8, $options);

        $this->assertSame([
            TitleOption::NAME => 'ApiGen It-self',
            SourceOption::NAME => [],
            DestinationOption::NAME => TEMP_DIR,
            AnnotationGroupsOption::NAME => [],
            BaseUrlOption::NAME => 'http://apigen.org',
            OverwriteOption::NAME => false,
            ThemeDirectoryOption::NAME => realpath(__DIR__ . '/../../packages/ThemeDefault/src'),
            VisibilityLevelOption::NAME => 768,
        ], $options);
    }

    public function testPrepareOptionsDestinationNotSet(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->configuration->resolveOptions([]);
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            DestinationOption::NAME => TEMP_DIR . '/api',
            SourceOption::NAME => [__DIR__],
        ];

        $options = $this->configuration->resolveOptions($configAndDestinationOptions);

        $this->assertSame(__DIR__, $options['source'][0]);
    }
}
