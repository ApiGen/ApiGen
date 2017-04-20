<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Template;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class TemplateFactoryTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $templateFactory = $this->container->getByType(TemplateFactoryInterface::class);
        $this->assertInstanceOf(Template::class, $templateFactory->create());
    }
}
