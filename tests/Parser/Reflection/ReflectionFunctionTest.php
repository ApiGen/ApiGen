<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use TokenReflection\Broker;

final class ReflectionFunctionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FunctionReflectionInterface
     */
    private $reflectionFunction;

    protected function setUp(): void
    {
        /** @var Backend $backend */
        $backend = $this->container->getByType(Backend::class);

        /** @var Broker $broker */
        $broker = $this->container->getByType(Broker::class);

        $broker->processDirectory(__DIR__ . '/ReflectionFunctionSource');

        $this->reflectionFunction = $backend->getFunctions()['getSomeData'];
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionFunction->isDocumented());
    }
}
