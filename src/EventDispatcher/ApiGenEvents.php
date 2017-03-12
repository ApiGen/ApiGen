<?php

namespace ApiGen\EventDispatcher;

/**
 * Contains all events thrown in the ApiGen.
 */
class ApiGenEvents
{

    /**
     * This event occurs when input options are resolved.
     *
     * The event listener method receives an ApiGen\EventDispatcher\Event\OptionsResolverEvent instance.
     *
     * @var string
     */
    const RESOLVE_OPTIONS = 'resolve.options';

    /**
     * This event occurs when generation process moves one step ahead.
     *
     * @var string
     */
    const STEP_INCREMENT = 'step.increment';
}
