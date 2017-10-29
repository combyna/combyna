<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface EventDispatcherInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDispatcherInterface
{
    /**
     * Dispatches an event
     *
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @param EventInterface $event
     * @param WidgetInterface $widget
     * @param WidgetEvaluationContextInterface $widgetEvaluationContext
     * @return ProgramStateInterface
     */
    public function dispatchEvent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetInterface $widget,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    );

    /**
     * Dispatches a signal
     *
     * @see DispatcherInterface::dispatchSignal()
     *
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @param SignalDefinitionInterface $signalDefinition
     * @param StaticBagInterface $payloadStaticBag
     * @return ProgramStateInterface
     */
    public function dispatchSignal(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        SignalDefinitionInterface $signalDefinition,
        StaticBagInterface $payloadStaticBag
    );
}
