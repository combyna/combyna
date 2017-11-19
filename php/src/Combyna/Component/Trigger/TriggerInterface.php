<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger;

use Combyna\Component\Event\EventInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;

/**
 * Interface TriggerInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TriggerInterface
{
    /**
     * Fetches the name of the library that defines the event that this trigger fires on
     *
     * @return string
     */
    public function getEventLibraryName();

    /**
     * Fetches the name of the event that this trigger fires on
     *
     * @return string
     */
    public function getEventName();

    /**
     * Invokes this trigger when the event it specifies is dispatched
     *
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @param EventInterface $event
     * @param WidgetEvaluationContextInterface $widgetEvaluationContext
     * @return ProgramStateInterface
     */
    public function invoke(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    );
}
