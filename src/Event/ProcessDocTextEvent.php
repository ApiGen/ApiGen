<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use Symfony\Component\EventDispatcher\Event;

final class ProcessDocTextEvent extends Event
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var ElementReflectionInterface
     */
    private $reflectionElement;

    public function __construct(string $text, ElementReflectionInterface $reflectionElement)
    {
        $this->text = $text;
        $this->reflectionElement = $reflectionElement;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getReflectionElement(): ElementReflectionInterface
    {
        return $this->reflectionElement;
    }

    public function changeText(string $text): void
    {
        $this->text = $text;
    }
}
