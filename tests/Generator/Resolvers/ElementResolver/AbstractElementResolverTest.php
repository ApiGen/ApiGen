<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

abstract class AbstractElementResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ElementResolverInterface
     */
    protected $elementResolver;

    /**
     * @var ParserInterface
     */
    protected $parser;

    protected function setUp(): void
    {
        $this->elementResolver = $this->container->getByType(ElementResolverInterface::class);
        $this->parser = $this->container->getByType(ParserInterface::class);
    }
}
