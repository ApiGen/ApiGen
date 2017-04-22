<?php declare(strict_types=1);

namespace ApiGen\Tests\Configuration;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
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
            ConfigurationOptions::DESTINATION => TEMP_DIR
        ]);

        $this->assertSame([
            'exclude' => [],
            'extensions' => ['php'],
            'title' => '',
            'googleAnalytics' => '',
            'config' => '',
            'overwrite' => false,
            'source' => [],
            'destination' => TEMP_DIR,
            'visibilityLevels' => 768,
            'annotationGroups' => [],
            'baseUrl' => '',
            'themeDirectory' => realpath(__DIR__ . '/../../packages/ThemeDefault/src')
        ], $options);
    }

    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsDestinationNotSet(): void
    {
        $this->configuration->prepareOptions([
            ConfigurationOptions::CONFIG => 'config.neon'
        ]);
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            ConfigurationOptions::CONFIG => __DIR__ . '/apigen.neon',
            ConfigurationOptions::DESTINATION => TEMP_DIR . '/api',
            ConfigurationOptions::SOURCE => [__DIR__]
        ];

        $options = $this->configuration->prepareOptions($configAndDestinationOptions);

        $this->assertSame(realpath(__DIR__ . '/../../src'), $options['source'][0]);
    }

    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = $this->configuration->prepareOptions([
            ConfigurationOptions::SOURCE => [__DIR__],
            ConfigurationOptions::CONFIG => __DIR__ . '/apigen.neon',
            ConfigurationOptions::DESTINATION => TEMP_DIR . '/api',
        ]);

        $this->assertSame(1792, $options[ConfigurationOptions::VISIBILITY_LEVELS]);
        $this->assertSame('http://apigen.org', $options[ConfigurationOptions::BASE_URL]);
    }
}
