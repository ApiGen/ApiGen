<?php declare(strict_types=1);

namespace ApiGen\Annotation\AnnotationSubscriber;

use ApiGen\Annotation\FqsenResolver\ElementResolver;
use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\Helpers\Strings as ApiGenStrings;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use phpDocumentor\Reflection\DocBlock\Tag;

final class SeeDescriptionSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;
    /**
     * @var ElementResolver
     */
    private $elementResolver;

    public function __construct(ReflectionRoute $reflectionRoute, LinkBuilder $linkBuilder, ElementResolver $elementResolver)
    {
        $this->reflectionRoute = $reflectionRoute;
        $this->linkBuilder = $linkBuilder;
        $this->elementResolver = $elementResolver;
    }

    /**
     * @param Tag|string $content
     */
    public function matches($content): bool
    {
        return is_string($content) && Strings::contains($content, '@see');
    }

    /**
     * @param string $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        return preg_replace_callback('~@(?:link|see)\\s+([^}]+)~', function ($matches) use ($reflection) {
            [$url, $description] = ApiGenStrings::split($matches[1]);
            $description = trim($description);

//            $link = $this->resolveLink($matches[1], $reflection);
//            if ($link) {
//                return $link;
//            }
//
            if (Validators::isUri($url)) {
                return $this->linkBuilder->build($url, $description ?: $url);
            }
        }, $content);
    }
}
