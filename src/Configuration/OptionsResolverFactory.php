<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface OptionsResolverFactory
{

    /**
     * @return OptionsResolver
     */
    public function create();
}
