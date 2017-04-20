<?php declare(strict_types=1);

namespace ApiGen\Progress;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;

final class StepCounter
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    public function __construct(ElementStorageInterface $elementStorage)
    {
        $this->elementStorage = $elementStorage;
    }

    public function getStepCount(): int
    {
        return $this->getSourceCodeStepCount()
            + count($this->elementStorage->getNamespaces())
            + count($this->elementStorage->getClasses())
            + count($this->elementStorage->getTraits())
            + count($this->elementStorage->getInterfaces())
            + count($this->elementStorage->getFunctions());
    }

    private function getSourceCodeStepCount(): int
    {
        return count($this->elementStorage->getClasses())
            + count($this->elementStorage->getInterfaces())
            + count($this->elementStorage->getTraits())
            + count($this->elementStorage->getExceptions())
            + count($this->elementStorage->getFunctions());
    }
}
