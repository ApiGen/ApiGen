<?php declare(strict_types=1);

namespace ApiGen\Tests\Theme;

use ApiGen\Configuration\Configuration;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Theme\ThemeResources;

final class ThemeResourcesTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ThemeResources
     */
    private $themeResources;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
        $this->themeResources = $this->container->getByType(ThemeResources::class);
    }

    public function testCopyToDestination(): void
    {
        $destinationDir = TEMP_DIR . '/destination';
        $this->configuration->resolveOptions([
            'source' => [__DIR__],
            'destination' => $destinationDir,
        ]);

        $this->themeResources->copyToDestination($destinationDir);
        $this->assertFileExists($destinationDir . '/resources/footer.png');
    }
}
