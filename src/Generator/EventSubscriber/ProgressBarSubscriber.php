<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\EventSubscriber;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\Event\QueueRunEvent;
use ApiGen\Generator\TemplateGenerators\ClassElementGenerator;
use ApiGen\Generator\TemplateGenerators\ConstantElementGenerator;
use ApiGen\Generator\TemplateGenerators\FunctionElementGenerator;
use ApiGen\Generator\TemplateGenerators\NamespaceGenerator;
use ApiGen\Generator\TemplateGenerators\PackageGenerator;
use ApiGen\Generator\TemplateGenerators\SourceCodeGenerator;


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
	public function getSubscribedEvents()
	{
		return [
			GeneratorEvents::ON_QUEUE_RUN => 'initProgressBar',
			GeneratorEvents::ON_GENERATE_PROGRESS => 'generateProgress'
		];
	}


	public function initProgressBar(QueueRunEvent $queueRunEvent)
	{
		$this->progressBar->init($queueRunEvent->getStepCount());
	}


	public function generateProgress(GenerateProgressEvent $generateProgressEvent)
	{
		$this->progressBar->increment(1);
	}

}
