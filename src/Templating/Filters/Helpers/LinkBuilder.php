<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use Latte\Runtime\Filters;
use Nette\Utils\Html;

class LinkBuilder
{

    /**
     * @param string $url
     * @param Html|string $text
     * @param bool $escape
     * @param array $classes
     * @return string
     */
    public function build($url, $text, $escape = true, array $classes = [])
    {
        return Html::el('a')->href($url)
            ->setHtml($escape ? Filters::escapeHtml($text) : $text)
            ->addAttributes(['class' => $classes])
            ->render();
    }
}
