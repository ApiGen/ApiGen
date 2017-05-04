<?php declare(strict_types=1);

namespace ApiGen\Tests\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\ModularConfiguration\Option\VisibilityLevelOption;
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
        $options = $this->configuration->resolveOptions([
            SourceOption::NAME => [],
            DestinationOption::NAME => TEMP_DIR
        ]);

        $this->assertSame([
            'source' => [],
            'visibilityLevels' => 768,
            'baseUrl' => 'http://apigen.org',
            'destination' => TEMP_DIR,
            'annotationGroups' => [],
            'exclude' => [],
            'extensions' => ['php'],
            'googleAnalytics' => '',
            'overwrite' => false,
            'themeDirectory' => realpath(__DIR__ . '/../../packages/ThemeDefault/src'),
            'title' => '',
        ], $options);
    }

    /**
     * @expectedException \ApiGen\ModularConfiguration\Exception\ConfigurationException
     */
    public function testPrepareOptionsDestinationNotSet(): void
    {
        $this->configuration->resolveOptions([]);
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            DestinationOption::NAME => TEMP_DIR . '/api',
            SourceOption::NAME => [__DIR__]
        ];

        $options = $this->configuration->resolveOptions($configAndDestinationOptions);

        $this->assertSame(__DIR__, $options['source'][0]);
    }

    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = $this->configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR . '/api',
        ]);

        $this->assertSame(768, $options[VisibilityLevelOption::NAME]);
        $this->assertSame('http://apigen.org', $options[BaseUrlOption::NAME]);
    }
}
