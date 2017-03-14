<?php

namespace ApiGen\Generator\EventSubscriber;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressBarSubscriber implements EventSubscriberInterface
{

    /**
     * @var ProgressBarInterface
     */
    private $progressBar;


    public function __construct(ProgressBarInterface $progressBar)
    {
        $this->progressBar = $progressBar;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            GenerateProgressEvent::class => 'generateProgress'
        ];
    }


    public function generateProgress()
    {
        $this->progressBar->increment(1);
    }
}
