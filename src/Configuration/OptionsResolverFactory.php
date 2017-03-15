<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class OptionsResolverFactory
{
    public function create(): OptionsResolver
    {
        return new OptionsResolver;
    }
}
