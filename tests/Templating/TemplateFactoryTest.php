<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Template;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class TemplateFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    protected function setUp(): void
    {
        $this->templateFactory = $this->container->getByType(TemplateFactoryInterface::class);

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
           'source' => [__DIR__],
           'destination' => __DIR__ . '/Destination'
        ]);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(Template::class, $this->templateFactory->create());
    }
}
