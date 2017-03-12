<?php

namespace ApiGen\Contracts;

interface VersionInterface
{

    /**
     * Returns version number.
     *
     * @return string
     */
    public function getVersion();
}
