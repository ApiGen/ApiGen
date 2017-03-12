<?php

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
    public function setStartLine($startLine)
    {
        $this->startLine = $startLine;
        return $this;
    }


    /**
     * @return int
     */
    public function getStartLine()
    {
        return $this->startLine;
    }


    /**
     * @param int $endLine
     * @return $this
     */
    public function setEndLine($endLine)
    {
        $this->endLine = $endLine;
        return $this;
    }


    /**
     * @return int
     */
    public function getEndLine()
    {
        return $this->endLine;
    }
}
