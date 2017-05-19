<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use Symfony\Component\EventDispatcher\Event;

final class ProcessDocTextEvent extends Event
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var AbstractReflectionInterface
     */
    private $reflection;

    public function __construct(string $text, AbstractReflectionInterface $reflection)
    {
        $this->text = $text;
        $this->reflection = $reflection;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getReflectionElement(): AbstractReflectionInterface
    {
        return $this->reflection;
    }

    public function changeText(string $text): void
    {
        $this->text = $text;
    }
}
