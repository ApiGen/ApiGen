<?php declare(strict_types=1);

namespace ApiGen\Event;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use Symfony\Component\EventDispatcher\Event;

class ProcessDocTextEvent extends Event
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

    public function getText()
    {
        return $this->text;
    }

    public function getReflectionElement()
    {
        return $this->reflectionElement;
    }

    public function changeText(string $text)
    {
        $this->text = $text;
    }
}
