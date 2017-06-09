<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract\Parameter;

interface ParameterProviderInterface
{
    /**
     * @return mixed[]
     */
    public function provide(): array;
}
