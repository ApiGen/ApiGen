<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber\SeeAnnotationSubscriberSource;

use ApiGen\Annotation\Tests\AnnotationDecoratorSource\ReturnedClass;

function anotherFunction(): void
{
}

/**
 * @see anotherFunction()
 */
function someFunction(): void
{
}
