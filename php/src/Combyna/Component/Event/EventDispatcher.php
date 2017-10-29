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
 * Class EventDispatcher
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetInterface $widget,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    ) {
        return $widget->dispatchEvent($programState, $program, $event, $widgetEvaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchSignal(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        SignalDefinitionInterface $signalDefinition,
        StaticBagInterface $payloadStaticBag
    ) {
        return $this->dispatcher->dispatchSignal(
            $program,
            $programState,
            $signalDefinition,
            $payloadStaticBag
        );
    }
}
