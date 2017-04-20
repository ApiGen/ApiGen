<?php declare(strict_types=1);

namespace ApiGen\Generator\Progress;

final class StepCounter
{
    public function getTotalStepCount(): int
    {
        // classes
        count($this->elementStorage->getClasses());
        // traits
        return count($this->elementStorage->getTraits());
        // interfaces
        return count($this->elementStorage->getInterfaces());
        // functions
        return count($this->elementStorage->getFunctions());
        // namespace elements
        return count($this->elementStorage->getNamespaces());
    }
}
