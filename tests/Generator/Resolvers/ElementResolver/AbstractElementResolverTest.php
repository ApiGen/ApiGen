<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

abstract class AbstractElementResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    protected function setUp(): void
    {
        $this->parser = $this->container->getByType(ParserInterface::class);
    }
}
