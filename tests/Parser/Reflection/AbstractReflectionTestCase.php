<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use TokenReflection\Broker;

abstract class AbstractReflectionTestCase extends AbstractContainerAwareTestCase
{
    /**
     * @var Broker
     */
    protected $broker;

    /**
     * @var Backend
     */
    protected $backend;

    /**
     * @var ParserInterface
     */
    protected $parser;

    protected function setUp(): void
    {
        $this->backend = $this->container->getByType(Backend::class);
        $this->broker = $this->container->getByType(Broker::class);
        $this->parser = $this->container->getByType(ParserInterface::class);
    }
}
