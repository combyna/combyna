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

use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Bag\StaticBagInterface;

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
     * @var SignalFactoryInterface
     */
    private $signalFactory;

    /**
     * @param SignalFactoryInterface $signalFactory
     */
    public function __construct(SignalFactoryInterface $signalFactory)
    {
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

        return $program->handleSignal($programState, $signal);
    }
}
