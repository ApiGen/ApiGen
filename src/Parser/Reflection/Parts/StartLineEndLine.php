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


    /**
     * @param int $startLine
     * @return $this
     */
    public function setStartLine(int $startLine)
    {
        $this->startLine = $startLine;
        return $this;
    }


    public function getStartLine(): int
    {
        return $this->startLine;
    }


    /**
     * @param int $endLine
     * @return $this
     */
    public function setEndLine(int $endLine)
    {
        $this->endLine = $endLine;
        return $this;
    }


    public function getEndLine(): int
    {
        return $this->endLine;
    }
}
