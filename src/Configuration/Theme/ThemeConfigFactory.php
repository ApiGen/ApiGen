<?php declare(strict_types=1);

namespace ApiGen\Configuration\Theme;

final class ThemeConfigFactory
{
    /**
     * @var ThemeConfigOptionsResolver
     */
    private $themeConfigOptionsResolver;

    public function __construct(ThemeConfigOptionsResolver $themeConfigOptionsResolver)
    {
        $this->themeConfigOptionsResolver = $themeConfigOptionsResolver;
    }

    public function create(string $filePath): ThemeConfig
    {
        return new ThemeConfig($filePath, $this->themeConfigOptionsResolver);
    }
}
