<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber\SeeAnnotationSubscriberSource;

function anotherFunction(): void
{
}

/**
 * Test.
 *
 * @see anotherFunction()
 */
function someFunction(): void
{
}
