<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Signal\EventDispatcher\Event\SignalDispatchedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Dispatcher
 *
 * Handles dispatching signals to all interested components of the app
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var SignalFactoryInterface
     */
    private $signalFactory;

    /**
     * @param SignalFactoryInterface $signalFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(SignalFactoryInterface $signalFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->signalFactory = $signalFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchSignal(
        ProgramInterface $program,
        ProgramStateInterface $programState,
        SignalDefinitionInterface $signalDefinition,
        StaticBagInterface $payloadStaticBag
    ) {
        $signal = $this->signalFactory->createSignal($signalDefinition, $payloadStaticBag);

        if ($signalDefinition->isBroadcast()) {
            // Dispatch an event to provide an extension point for triggering RPC Ajax requests, etc.
            $this->eventDispatcher->dispatch(
                SignalEvents::BROADCAST_SIGNAL_DISPATCHED,
                new SignalDispatchedEvent(
                    $signal
                )
            );
        }

        return $program->handleSignal($programState, $signal);
    }
}
