<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\Reflector;

use ApiGen\BetterReflection\SourceLocator\SourceLocatorsFactory;
use Roave\BetterReflection\Reflector\FunctionReflector;

final class FunctionReflectorFactory
{
    /**
     * @var SourceLocatorsFactory
     */
    private $sourceLocatorsFactory;

    /**
     * @var ClassReflectorFactory
     */
    private $classReflectorFactory;

    public function __construct(
        SourceLocatorsFactory $sourceLocatorsFactory,
        ClassReflectorFactory $classReflectorFactory
    ) {
        $this->sourceLocatorsFactory = $sourceLocatorsFactory;
        $this->classReflectorFactory = $classReflectorFactory;
    }

    public function create(): FunctionReflector
    {
        return new FunctionReflector(
            $this->sourceLocatorsFactory->create(),
            $this->classReflectorFactory->create()
        );
    }
}
