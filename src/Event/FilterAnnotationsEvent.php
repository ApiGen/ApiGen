<?php declare(strict_types=1);

namespace ApiGen\Event;

use Symfony\Component\EventDispatcher\Event;

final class FilterAnnotationsEvent extends Event
{
    /**
     * @var mixed[]
     */
    private $annotations;

    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    public function changeAnnotations(array $annotations)
    {
        $this->annotations = $annotations;
    }
}
