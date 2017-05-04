<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;
use Symfony\Component\EventDispatcher\Event;

final class ProcessDocTextEvent extends Event
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var ReflectionInterface
     */
    private $reflectionElement;

    public function __construct(string $text, ReflectionInterface $reflectionElement)
    {
        $this->text = $text;
        $this->reflectionElement = $reflectionElement;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getReflectionElement(): ReflectionInterface
    {
        return $this->reflectionElement;
    }

    public function changeText(string $text): void
    {
        $this->text = $text;
    }
}
