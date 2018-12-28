<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers;

use ApiGen\Configuration\Configuration;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Utils\RelativePathResolver;
use InvalidArgumentException;

final class RelativePathResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RelativePathResolver
     */
    private $relativePathResolver;

    protected function setUp(): void
    {
        $this->configuration = $this->container->get(Configuration::class);
        $this->relativePathResolver = $this->container->get(RelativePathResolver::class);
    }

    public function testGetRelativePath(): void
    {
        $this->configuration->resolveOptions([
            DestinationOption::NAME => TEMP_DIR,
            SourceOption::NAME => [TEMP_DIR],
        ]);

        $this->assertSame(
            'some-file.txt',
            $this->relativePathResolver->getRelativePath(sprintf('%s/some-file.txt', TEMP_DIR))
        );

        $testPath = sprintf('some%sdir%sfile.txt', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $this->assertSame(
            $testPath,
            $this->relativePathResolver->getRelativePath(TEMP_DIR . DIRECTORY_SEPARATOR . $testPath)
        );
    }

    public function testGetRelativePathInvalid(): void
    {
        $this->configuration->resolveOptions([
            DestinationOption::NAME => TEMP_DIR,
            SourceOption::NAME => [TEMP_DIR],
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->relativePathResolver->getRelativePath('/var/dir/some-strange-file.txt');
    }
}
