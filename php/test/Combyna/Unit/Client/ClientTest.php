<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Client;

use Combyna\Client\Client;
use Combyna\Component\App\AppInterface;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Framework\EventDispatcher\Event\AppStateUpdatedEvent;
use Combyna\Component\Framework\FrameworkEvents;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClientTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ClientTest extends TestCase
{
    /**
     * @var ObjectProphecy&AppInterface
     */
    private $app;

    /**
     * @var ObjectProphecy&ArrayRenderer
     */
    private $arrayRenderer;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ObjectProphecy&Combyna
     */
    private $combyna;

    /**
     * @var ObjectProphecy&EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ObjectProphecy&AppStateInterface
     */
    private $initialAppState;

    public function setUp()
    {
        $this->app = $this->prophesize(AppInterface::class);
        $this->arrayRenderer = $this->prophesize(ArrayRenderer::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->combyna = $this->prophesize(Combyna::class);
        $this->initialAppState = $this->prophesize(AppStateInterface::class);

        $this->client = new Client(
            $this->eventDispatcher->reveal(),
            $this->combyna->reveal(),
            $this->arrayRenderer->reveal(),
            $this->app->reveal(),
            $this->initialAppState->reveal()
        );
    }

    public function testOnBroadcastSignalAsksCombynaToAddTheListener()
    {
        $callback = function () {};

        $this->client->onBroadcastSignal($callback);

        $this->combyna->onBroadcastSignal(Argument::is($callback))
            ->shouldHaveBeenCalledOnce();
    }

    public function testOnRouteNavigatedAsksCombynaToAddTheListener()
    {
        $callback = function () {};

        $this->client->onRouteNavigated($callback);

        $this->combyna->onRouteNavigated(Argument::is($callback))
            ->shouldHaveBeenCalledOnce();
    }

    public function testUpdateAppStateUpdatesTheCurrentAppStateForClient()
    {
        $newAppState = $this->prophesize(AppStateInterface::class);

        $this->client->updateAppState($newAppState->reveal());

        static::assertSame($newAppState->reveal(), $this->client->getCurrentAppState());
    }

    public function testUpdateAppStateDispatchesAnEventForTheStateUpdate()
    {
        $newAppState = $this->prophesize(AppStateInterface::class);

        $this->client->updateAppState($newAppState->reveal());

        $this->eventDispatcher->dispatch(
            FrameworkEvents::APP_STATE_UPDATED,
            new AppStateUpdatedEvent($newAppState->reveal())
        )
            ->shouldHaveBeenCalledOnce();
    }
}
