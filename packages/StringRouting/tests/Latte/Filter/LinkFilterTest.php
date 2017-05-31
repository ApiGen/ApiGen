<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Latte\Filter;

use ApiGen\Tests\AbstractContainerAwareTestCase;
use Latte\Engine;
use Nette\InvalidArgumentException;

final class LinkFilterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Engine
     */
    private $latte;

    protected function setUp(): void
    {
        $this->latte = $this->container->getByType(Engine::class);
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument for filter "linkSource" has to be type of ' .
            '"ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface". "string" given.'
        );

        $this->latte->renderToString(__DIR__ . '/Source/template.latte', [
            'classReflection' => 'SomeClass'
        ]);
    }
}
