<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Annotation\AnnotationSubscriber\LinkAnnotationSubscriberSource\SomeClassWithLinkAnnotations;

final class LinkAnnotationSubscriberTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/LinkAnnotationSubscriberSource']);
        $this->annotationDecorator = $this->container->getByType(AnnotationDecorator::class);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithLinkAnnotations::class];
    }

    public function testUrl(): void
    {
        $seeAnnotation = $this->classReflection->getAnnotation(AnnotationList::LINK)[0];
        $decoratedSeeAnnotation = $this->annotationDecorator->decorate($seeAnnotation, $this->classReflection);

        $this->assertSame(
            '<a href="http://php.net/session_set_save_handler">http://php.net/session_set_save_handler</a>',
            $decoratedSeeAnnotation
        );
    }


//    @todo: provide data method

//[
//'{@link bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK Donations}',
//'<a href="bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK">Donations</a>'
//],
//['@link http://apigen.org Description', '<a href="http://apigen.org">Description</a>'],
//['{@link http://apigen.org Description}', '<a href="http://apigen.org">Description</a>'],
//['{@link http://apigen.org}', '<a href="http://apigen.org">http://apigen.org</a>'],


}
