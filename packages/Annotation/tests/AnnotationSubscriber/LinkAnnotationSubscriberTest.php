<?php declare(strict_types=1);

namespace ApiGen\Annotation\Tests\AnnotationSubscriber;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Annotation\Tests\AnnotationSubscriber\LinkAnnotationSubscriberSource\SomeClassWithLinkAnnotations;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Link;

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
        $this->parser->parseFilesAndDirectories([__DIR__ . '/LinkAnnotationSubscriberSource']);
        $this->annotationDecorator = $this->container->get(AnnotationDecorator::class);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithLinkAnnotations::class];
    }

    public function testReflectionAnnotation(): void
    {
        $linkAnnotation = $this->classReflection->getAnnotation(AnnotationList::LINK)[0];
        $decoratedAnnotation = $this->annotationDecorator->decorate($linkAnnotation, $this->classReflection);

        $this->assertSame(
            '<a href="http://php.net/session_set_save_handler">http://php.net/session_set_save_handler</a>',
            $decoratedAnnotation
        );
    }

    public function testSeeUrlAnnotation(): void
    {
        $seeUrlAnnotation = $this->classReflection->getAnnotation(AnnotationList::SEE)[0];
        $decoratedAnnotation = $this->annotationDecorator->decorate($seeUrlAnnotation, $this->classReflection);

        $this->assertSame(
            '<a href="https://github.com/apigen/apigen">https://github.com/apigen/apigen</a>',
            $decoratedAnnotation
        );
    }

    /**
     * @dataProvider getLinkAnnotationData()
     */
    public function testUrl(string $link, string $description, string $expectedOutput): void
    {
        $linkAnnotation = new Link($link, $description ? new Description($description) : null);

        $this->assertSame(
            $expectedOutput,
            $this->annotationDecorator->decorate($linkAnnotation, $this->classReflection)
        );
    }

    /**
     * @return string[][]
     */
    public function getLinkAnnotationData(): array
    {
        return [
            [
                'http://php.net/session_set_save_handler',
                '',
                '<a href="http://php.net/session_set_save_handler">http://php.net/session_set_save_handler</a>',
            ],
            [
                'bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK',
                'Donations',
                '<a href="bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK">Donations</a>',
            ],
            ['http://licence.com', 'MIT', '<a href="http://licence.com">MIT</a>'],
            ['https://apigen.org', 'Description', '<a href="https://apigen.org">Description</a>'],
        ];
    }
}
