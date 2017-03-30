<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

abstract class AbstractElementResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ElementResolverInterface
     */
    protected $elementResolver;

    /**
     * @var ParserStorageInterface
     */
    protected $parserStorage;

    protected function setUp(): void
    {
        $this->elementResolver = $this->container->getByType(ElementResolverInterface::class);
        $this->parserStorage = $this->container->getByType(ParserStorageInterface::class);
    }
}
