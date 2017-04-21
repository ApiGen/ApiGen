<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Templating\Template;
use Symfony\Component\EventDispatcher\Event;

final class CreateTemplateEvent extends Event
{
    /**
     * @var Template
     */
    private $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }
}
