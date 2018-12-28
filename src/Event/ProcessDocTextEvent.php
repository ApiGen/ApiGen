<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use Symfony\Component\EventDispatcher\Event;

final class ProcessDocTextEvent extends Event
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var AnnotationsInterface|AbstractReflectionInterface
     */
    private $reflection;

    /**
     * @param AnnotationsInterface|AbstractReflectionInterface $text
     */
    public function __construct(string $text, AnnotationsInterface $annotations)
    {
        $this->text = $text;
        $this->reflection = $annotations;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return AnnotationsInterface|AbstractReflectionInterface
     */
    public function getReflectionElement(): AnnotationsInterface
    {
        return $this->reflection;
    }

    public function changeText(string $text): void
    {
        $this->text = $text;
    }
}
