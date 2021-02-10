<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Signal;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Signal\Dispatcher;
use Combyna\Component\Signal\EventDispatcher\Event\SignalDispatchedEvent;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Signal\SignalEvents;
use Combyna\Component\Signal\SignalFactoryInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DispatcherTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DispatcherTest extends TestCase
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var ObjectProphecy|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ObjectProphecy|StaticBagInterface
     */
    private $payloadStaticBag;

    /**
     * @var ObjectProphecy|ProgramInterface
     */
    private $program;

    /**
     * @var ObjectProphecy|ProgramStateInterface
     */
    private $programState;

    /**
     * @var ObjectProphecy|SignalDefinitionInterface
     */
    private $signalDefinition;

    /**
     * @var ObjectProphecy|SignalFactoryInterface
     */
    private $signalFactory;

    public function setUp()
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->payloadStaticBag = $this->prophesize(StaticBagInterface::class);
        $this->program = $this->prophesize(ProgramInterface::class);
        $this->programState = $this->prophesize(ProgramStateInterface::class);
        $this->signalDefinition = $this->prophesize(SignalDefinitionInterface::class);
        $this->signalFactory = $this->prophesize(SignalFactoryInterface::class);

        $this->signalDefinition->isBroadcast()->willReturn(false);

        $this->dispatcher = new Dispatcher($this->signalFactory->reveal(), $this->eventDispatcher->reveal());
    }

    public function testDispatchSignalDispatchesAnEventForTheSignalWhenMarkedForBroadcast()
    {
        /** @var ObjectProphecy|SignalInterface $signal */
        $signal = $this->prophesize(SignalInterface::class);
        $this->signalFactory->createSignal(
            Argument::is($this->signalDefinition->reveal()),
            Argument::is($this->payloadStaticBag->reveal())
        )->willReturn($signal);
        $this->signalDefinition->isBroadcast()->willReturn(true);

        $this->dispatcher->dispatchSignal(
            $this->program->reveal(),
            $this->programState->reveal(),
            $this->signalDefinition->reveal(),
            $this->payloadStaticBag->reveal()
        );

        $this->eventDispatcher->dispatch(
            SignalEvents::BROADCAST_SIGNAL_DISPATCHED,
            new SignalDispatchedEvent($signal->reveal())
        )
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testDispatchSignalDoesNotDispatchAnEventForTheSignalWhenNotMarkedForBroadcast()
    {
        /** @var ObjectProphecy|SignalInterface $signal */
        $signal = $this->prophesize(SignalInterface::class);
        $this->signalFactory->createSignal(
            Argument::is($this->signalDefinition->reveal()),
            Argument::is($this->payloadStaticBag->reveal())
        )->willReturn($signal);
        $this->signalDefinition->isBroadcast()->willReturn(false);

        $this->dispatcher->dispatchSignal(
            $this->program->reveal(),
            $this->programState->reveal(),
            $this->signalDefinition->reveal(),
            $this->payloadStaticBag->reveal()
        );

        $this->eventDispatcher->dispatch(
            SignalEvents::BROADCAST_SIGNAL_DISPATCHED,
            new SignalDispatchedEvent($signal->reveal())
        )
            ->shouldNotHaveBeenCalled();
    }
}
