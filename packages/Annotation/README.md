# Annotation Module

Here, we deal with **doc block description**, **annotations** and their modification.

```php
/**
 * Hey, I'm description. You can change me
 * if you like.
 *
 * @see AnotherSexyClass This class is muse to me.
 * @author Me <ego@gmail.com>
 */
```

**Do you need to add some extra changes?** 

Here are you options: 


## How to Modify Description

Sometimes you need to modify your description: decorate HTML, Markdown or anything else:

```php
/** 
 * This is how to use this:
 * - one
 * - two
 * - three
 * And last example: **four**
 */
```

How to get there? Use an event subscriber and listen to [`ApiGen\Event\ProcessDocTextEvent`](/src/Event/ProcessDocTextEvent.php).

1. Create class that implements `Symfony\Component\EventDispatcher\EventSubscriberInterface`:

```php
namespace App\ApiGen\EventSubscriber;

use ApiGen\Event\ProcessDocTextEvent;
use Parsedown;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MarkdownDescriptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var Parsedown
     */
    private $parsedown;
    
    // require any service you need in constructor
    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }
    
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessDocTextEvent::class => 'decorateDescriptionWithMarkdown'
        ];
    }
    
    public function decorateDescriptionWithMarkdown(ProcessDocTextEvent $processDocTextEvent)
    {
        $text = $processDocTextEvent->getText();
        // you can start with this, but it might require some more work
        $decoratedText = $this->parsedown->text($text);
        // return changed text to the event, because only that will be used by ApiGen
        $processDocTextEvent->changeText($decoratedText);
    }
}
```

2. Register subscriber and everything else you need as service in `apigen.yml`:

```yaml
services:
    _defaults:
        autowire: true
   
    App\ApiGen\EventSubscriber\MarkdownDescriptionSubscriber: ~ 
    Parsedown: ~
```

That's it!


## How to Render Custom Annotation

For example, to work with `@throws` annotation all you need is to follow these 2 steps:

1. Create class that implements `ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface`: 

```php
namespace App\ApiGen\AnnotationSubscriber\ThrowsAnnotationSubscriber;

use ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Utils\LinkBuilder;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

final class ThrowsAnnotationSubscriber implements AnnotationSubscriberInterface
{
    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    // require any service you need in constructor
    public function __construct(LinkBuilder $linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @param Tag|string $content
     */
    public function matches($content): bool
    {
        // match your tag
        return $content instanceof Throws;
    }

    /**
     * @param Throws $content
     */
    public function process($content, AbstractReflectionInterface $reflection): string
    {
        // do what you need here
        // here we create simple link to type (pseudo code) 
        return $this->linkBuilder->build($content->getType(), (string) $content);
    }
}
```

2. Register it as a services in `apigen.yml`

```yaml
services:
    _defaults:
        autowire: true
    
    App\ApiGen\AnnotationSubscriber\ThrowsAnnotationSubscriber: ~
```

That's it!

These classes have to autoloadable by composer.
