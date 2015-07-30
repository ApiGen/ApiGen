<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
