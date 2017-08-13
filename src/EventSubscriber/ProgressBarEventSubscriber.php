<?php declare(strict_types=1);

namespace ApiGen\EventSubscriber;

use ApiGen\Console\Helper\ProgressBar;
use ApiGen\Event\GenerateProgressEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProgressBarEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GenerateProgressEvent::class => 'generateProgress',
        ];
    }

    public function generateProgress(): void
    {
        $this->progressBar->increment(1);
    }
}
