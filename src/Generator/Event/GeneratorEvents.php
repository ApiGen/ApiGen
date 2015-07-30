<?php

/**
 * This file is part of Generator
 *
 * Copyright (c) 2014 Pears Health Cyber, s.r.o. (http://pearshealthcyber.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Event;

class GeneratorEvents
{

    /**
     * @var string
     */
    const ON_QUEUE_RUN = 'onQueueRun';

    /**
     * @var string
     */
    const ON_GENERATE_PROGRESS = 'onProgress';
}
