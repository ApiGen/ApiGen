<?php declare(strict_types=1);

namespace ApiGen\Tests\Console;

use ApiGen\Console\Application;
use ApiGen\Tests\ContainerAwareTestCase;

final class ApplicationTest extends ContainerAwareTestCase
{
    public function testsGetLongVersions(): void
    {
        $application = $this->container->getByType(Application::class);

        $this->assertSame('ApiGen', $application->getLongVersion());
    }
}
