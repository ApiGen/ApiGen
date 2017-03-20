<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use Latte\Runtime\Filters;
use Nette\Utils\Html;

final class LinkBuilder
{
    /**
     * @param string $url
     * @param Html|string $text
     * @param bool $escape
     * @param string[] $classes
     */
    public function build(string $url, $text, bool $escape = true, array $classes = []): string
    {
        return Html::el('a')->href($url)
            ->setHtml($escape ? Filters::escapeHtml($text) : $text)
            ->addAttributes(['class' => $classes])
            ->render();
    }
}
