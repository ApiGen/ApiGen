<?php declare(strict_types=1);

namespace ApiGen\Tests\Configuration;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ConfigurationTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(ConfigurationInterface::class);
    }

    public function testResolve(): void
    {
        $options = $this->configuration->prepareOptions([
            DestinationOption::NAME => TEMP_DIR
        ]);

        $this->assertSame([
            'exclude' => [],
            'extensions' => ['php'],
            'title' => '',
            'googleAnalytics' => '',
            'overwrite' => false,
            'source' => [],
            'visibilityLevels' => 768,
            'themeDirectory' => realpath(__DIR__ . '/../../packages/ThemeDefault/src'),
            'destination' => TEMP_DIR,
            'config' => '',
            'annotationGroups' => [],
            'baseUrl' => '',
        ], $options);
    }

    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsDestinationNotSet(): void
    {
        $this->configuration->prepareOptions([
            ConfigurationFileOption::NAME => 'config.neon'
        ]);
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            ConfigurationFileOption::NAME => __DIR__ . '/apigen.neon',
            DestinationOption::NAME => TEMP_DIR . '/api',
            ConfigurationOptions::SOURCE => [__DIR__]
        ];

        $options = $this->configuration->prepareOptions($configAndDestinationOptions);

        $this->assertSame(realpath(__DIR__ . '/../../src'), $options['source'][0]);
    }

    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = $this->configuration->prepareOptions([
            ConfigurationOptions::SOURCE => [__DIR__],
            ConfigurationFileOption::NAME => __DIR__ . '/apigen.neon',
            DestinationOption::NAME => TEMP_DIR . '/api',
        ]);

        $this->assertSame(1792, $options[ConfigurationOptions::VISIBILITY_LEVELS]);
        $this->assertSame('http://apigen.org', $options[BaseUrlOption::NAME]);
    }
}
