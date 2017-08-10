<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Partial\Source;

/**
 * This is some description.
 *
 * @see \ApiGen\Application\ApiGenApplication
 * @author Everyone.
 * @license MIT
 */
class SomeClassWithAnnotations
{
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
}
