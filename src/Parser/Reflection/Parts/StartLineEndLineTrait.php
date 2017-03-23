<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Parts;

trait StartLineEndLineTrait
{
    /**
     * @var int
     */
    private $startLine;

    /**
     * @var int
     */
    private $endLine;

    public function setStartLine(int $startLine): void
    {
        $this->startLine = $startLine;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function setEndLine(int $endLine): void
    {
        $this->endLine = $endLine;
    }

    public function getEndLine(): int
    {
        return $this->endLine;
    }
}
