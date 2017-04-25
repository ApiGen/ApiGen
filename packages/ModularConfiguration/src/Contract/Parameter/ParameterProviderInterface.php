<?php

namespace ApiGen\ModularConfiguration\Contract\Parameter;

interface ParameterProviderInterface
{
    /**
     * @return mixed[]
     */
    public function provide(): array;
}
