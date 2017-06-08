<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\IndexGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class OverviewGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var IndexGenerator $overviewGenerator */
        $overviewGenerator = $this->container->get(IndexGenerator::class);
        $overviewGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/index.html');
    }
}
