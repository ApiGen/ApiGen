<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\Reflector;

use ApiGen\BetterReflection\SourceLocator\SourceLocatorsFactory;
use Roave\BetterReflection\Reflector\ClassReflector;

final class ClassReflectorFactory
{
    /**
     * @var SourceLocatorsFactory
     */
    private $sourceLocatorsFactory;

    public function __construct(SourceLocatorsFactory $sourceLocatorsFactory)
    {
        $this->sourceLocatorsFactory = $sourceLocatorsFactory;
    }

    public function create(): ClassReflector
    {
        return new ClassReflector($this->sourceLocatorsFactory->create());
    }
}
