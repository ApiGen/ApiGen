<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class OverviewGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var OverviewGenerator $overviewGenerator */
        $overviewGenerator = $this->container->getByType(OverviewGenerator::class);
        $overviewGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/index.html');
    }
}
