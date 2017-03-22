<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Tests\MethodInvoker;

final class SimpleTypeTest extends AbstractElementResolverTest
{
    public function test(): void
    {
        $this->assertTrue(MethodInvoker::callMethodOnObject($this->elementResolver, 'isSimpleType', ['string']));
        $this->assertTrue(MethodInvoker::callMethodOnObject($this->elementResolver, 'isSimpleType', ['boolean']));
        $this->assertTrue(MethodInvoker::callMethodOnObject($this->elementResolver, 'isSimpleType', ['NULL']));
        $this->assertTrue(MethodInvoker::callMethodOnObject($this->elementResolver, 'isSimpleType', ['']));
        $this->assertFalse(MethodInvoker::callMethodOnObject($this->elementResolver, 'isSimpleType', ['DateTime']));
    }
}
