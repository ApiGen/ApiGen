<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Event\CreateTemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConfigurationTemplateVariablesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [CreateTemplateEvent::class => 'loadTemplateVariables'];
    }

    public function loadTemplateVariables(CreateTemplateEvent $createTemplateEvent): void
    {
        $template = $createTemplateEvent->getTemplate();
        $template->setParameters([
            'title' => $this->configuration->getTitle(),
            'googleAnalytics' => $this->configuration->getGoogleAnalytics(),
            'annotationGroups' => $this->configuration->getAnnotationGroups()
        ]);
    }
}