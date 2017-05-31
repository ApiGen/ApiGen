<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Templating\Parameters\ParameterBag;
use Symfony\Component\EventDispatcher\Event;

final class CreateTemplateEvent extends Event
{
    /**
     * @var ParameterBag
     */
    private $parameterBag;

    public function __construct(ParameterBag $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }
}
