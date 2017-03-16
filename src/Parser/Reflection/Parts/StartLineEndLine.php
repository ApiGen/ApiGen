<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Parts;

trait StartLineEndLine
{
    /**
     * @var int
     */
    private $startLine;

    /**
     * @var int
     */
    private $endLine;


    public function setStartLine(int $startLine)
    {
        $this->startLine = $startLine;
    }


    public function getStartLine(): int
    {
        return $this->startLine;
    }


    public function setEndLine(int $endLine)
    {
        $this->endLine = $endLine;
    }


    public function getEndLine(): int
    {
        return $this->endLine;
    }
}
