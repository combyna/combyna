<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;

/**
 * Interface DispatcherInterface
 *
 * Handles dispatching signals to all interested components of the app
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DispatcherInterface
{
    /**
     * Dispatches a signal to all components of the app, returning the resulting complete app state
     * If no changes are made to the app's state in response to the signal, then the same instance
     * of ProgramState that was passed in may be returned, as states are immutable
     *
     * @param ProgramInterface $program
     * @param ProgramStateInterface $programState
     * @param SignalDefinitionInterface $signalDefinition
     * @param StaticBagInterface $payloadStaticBag
     * @return ProgramStateInterface
     */
    public function dispatchSignal(
        ProgramInterface $program,
        ProgramStateInterface $programState,
        SignalDefinitionInterface $signalDefinition,
        StaticBagInterface $payloadStaticBag
    );
}
