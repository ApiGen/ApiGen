<?php declare(strict_types=1);

namespace ApiGen\Reflection\DocBlock;

use ApiGen\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlockFactory as PhpDocumentorDocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class DocBlockFactory
{
    /**
     * @var PhpDocumentorDocBlockFactory
     */
    private $phpDocumentorDocBlockFactory;

    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @var DescriptionFactory
     */
    private $descriptionFactory;

    public function __construct(TagFactory $tagFactory, PhpDocumentorDocBlockFactory $docBlockFactory, DescriptionFactory $descriptionFactory)
    {
        $this->phpDocumentorDocBlockFactory = $docBlockFactory;
        $this->tagFactory = $tagFactory;
        $this->descriptionFactory = $descriptionFactory;

        // @todo: move to services.yml
        $this->tagFactory->registerTagHandler('see', See::class);
        $this->tagFactory->addService($this->descriptionFactory);

    }

    public function createFromBetterReflection(ReflectionClass $classReflection): DocBlock
    {
        return $this->phpDocumentorDocBlockFactory->create($classReflection->getDocComment() ?: ' ');
    }
}
