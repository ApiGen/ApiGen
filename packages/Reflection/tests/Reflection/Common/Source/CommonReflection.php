<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Common\Source;

use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\SomeOtherClass;

/**
 * This is some description.
 */
class CommonReflection
{
    /**
     * @var string
     */
    public const THIS_CLASS_METHOD = __CLASS__ . '::methodWithArgs';

    /**
     * @var string
     */
    public const THIS_DIRECTORY = __DIR__;

    /**
     * Send a POST request.
     *
     * @param int|string $url the URL of the API endpoint
     * @param mixed $data and array or a blob of data to be sent
     * @param mixed[] $headers add optional headers
     */
    public function methodWithArgs($url = 1, $data = null, $headers = []): void
    {
    }

    public function getClass(): string
    {
        return SomeOtherClass::class;
    }
}
