<?php declare(strict_types=1);

namespace ApiGen\Event;

use Symfony\Component\EventDispatcher\Event;

final class FilterAnnotationsEvent extends Event
{
    /**
     * @var mixed[]
     */
    private $annotations;

    /**
     * @param mixed[] $annotations
     */
    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    /**
     * @param mixed[] $annotations
     */
    public function changeAnnotations(array $annotations): void
    {
        $this->annotations = $annotations;
    }
}
