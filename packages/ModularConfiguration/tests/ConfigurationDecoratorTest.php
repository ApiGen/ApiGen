<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Tests;

use ApiGen\ModularConfiguration\Contract\ConfigurationDecoratorInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ConfigurationDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ConfigurationDecoratorInterface
     */
    private $configurationDecorator;

    protected function setUp()
    {
        $this->configurationDecorator = $this->container->getByType(ConfigurationDecoratorInterface::class);
    }

    public function test()
    {
        // $this->configurationDecorator
    }
}
