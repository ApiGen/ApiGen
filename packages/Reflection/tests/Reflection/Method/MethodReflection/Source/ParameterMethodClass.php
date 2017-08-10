<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source;

class ParameterMethodClass
{
    /**
     * @var string
     */
    private const HERE = 'here';

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

    public function methodWithClassParameter(ParameterClass $parameterClass): void
    {
    }

    public function methodWithConstantDefaultValue(string $where = self::HERE, string $when = Time::TODAY): void
    {
    }
}
