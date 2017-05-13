<?php declare(strict_types=1);

namespace ApiGen\Element\Annotation;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

final class SingleAnnotationStorage
{
    /**
     * @var string
     */
    private $annotation;

    /**
     * @var ClassReflectionInterface[]
     */
    private $classReflections;

    /**
     * @var InterfaceReflectionInterface[]
     */
    private $interfaceReflections;

    /**
     * @var TraitReflectionInterface[]
     */
    private $traitReflections;

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functionReflections;

    /**
     * @var ClassReflectionInterface[]
     */
    private $classOrTraitMethodReflections;

    /**
     * @var ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    private $classOrTraitPropertyReflections;

    /**
     * @var ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    private $classOrInterfaceConstantReflections;

    /**
     * @param ClassReflectionInterface[] $classReflections
     * @param InterfaceReflectionInterface[] $interfaceReflections
     * @param TraitReflectionInterface[] $traitReflections
     * @param FunctionReflectionInterface[] $functionReflections
     * @param ClassReflectionInterface[] $classOrTraitMethodReflections
     * @param ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[] $classOrTraitPropertyReflections
     * @param ClassReflectionInterface[]|InterfaceReflectionInterface[] $classOrInterfaceConstantReflections
     */
    public function __construct(
        string $annotation,
        array $classReflections,
        array $interfaceReflections,
        array $traitReflections,
        array $functionReflections,
        array $classOrTraitMethodReflections,
        array $classOrTraitPropertyReflections,
        array $classOrInterfaceConstantReflections
    ) {
        $this->annotation = $annotation;
        $this->classReflections = $classReflections;
        $this->interfaceReflections = $interfaceReflections;
        $this->traitReflections = $traitReflections;
        $this->functionReflections = $functionReflections;
        $this->classOrTraitMethodReflections = $classOrTraitMethodReflections;
        $this->classOrTraitPropertyReflections = $classOrTraitPropertyReflections;
        $this->classOrInterfaceConstantReflections = $classOrInterfaceConstantReflections;
    }



//'annotation' => $annotation,
//                'hasElements' => (bool) count(array_filter($elements, 'count')),
//                'classes' => $elements['classes'],
//                'interfaces' => $elements['interfaces'],
//                'traits' => $elements['traits'],
//                'methods' => $elements['methods'],
//                'functions' => $elements['functions'],
//                'properties' => $elements['properties']


}
