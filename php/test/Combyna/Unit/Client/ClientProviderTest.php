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

use Closure;
use Combyna\Client\Client;
use Combyna\Client\ClientProvider;
use Combyna\Component\App\AppInterface;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Component\Ui\Environment\Library\GenericWidgetValueProviderInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClientProviderTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ClientProviderTest extends TestCase
{
    /**
     * @var ObjectProphecy|AppInterface
     */
    private $app;

    /**
     * @var ObjectProphecy|ArrayRenderer
     */
    private $arrayRenderer;

    /**
     * @var ObjectProphecy|Combyna
     */
    private $combyna;

    /**
     * @var ObjectProphecy|EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var ObjectProphecy|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ClientProvider
     */
    private $provider;

    /**
     * @var ObjectProphecy|GenericWidgetValueProviderInterface
     */
    private $widgetValueProvider;

    public function setUp()
    {
        $this->app = $this->prophesize(AppInterface::class);
        $this->arrayRenderer = $this->prophesize(ArrayRenderer::class);
        $this->combyna = $this->prophesize(Combyna::class);
        $this->environmentNode = $this->prophesize(EnvironmentNode::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->widgetValueProvider = $this->prophesize(GenericWidgetValueProviderInterface::class);

        $this->combyna
            ->createApp(['app' => 'yep'], $this->environmentNode)
            ->willReturn($this->app->reveal());
        $this->combyna
            ->createEnvironment(
                ['libraries' => [
                    ['lib' => 1],
                    ['lib' => 2]
                ]]
            )
            ->willReturn($this->environmentNode);
        $this->combyna->onBroadcastSignal(Argument::type(Closure::class))->willReturn();
        $this->combyna->onRouteNavigated(Argument::type(Closure::class))->willReturn();
        $this->combyna->useProductionMode()->willReturn();

        $this->provider = new ClientProvider(
            $this->eventDispatcher->reveal(),
            $this->combyna->reveal(),
            $this->arrayRenderer->reveal(),
            $this->widgetValueProvider->reveal()
        );
    }

    public function testAddWidgetValueProviderDelegatesToTheGenericProvider()
    {
        $myCallable = function () {};

        $this->provider->addWidgetValueProvider(
            'my_library',
            'my_widget',
            'my_value',
            $myCallable
        );

        $this->widgetValueProvider->addProvider(
            'my_library',
            'my_widget',
            'my_value',
            $myCallable
        )->shouldHaveBeenCalledOnce();
    }

    public function testCreateClientReturnsANewClient()
    {
        $initialState = $this->prophesize(AppStateInterface::class);
        $this->app->createInitialState()->willReturn($initialState);

        $client = $this->provider->createClient(
            ['libraries' => [
                ['lib' => 1],
                ['lib' => 2]
            ]],
            ['app' => 'yep']
        );

        static::assertInstanceOf(Client::class, $client);
        static::assertSame($initialState->reveal(), $client->getCurrentAppState());
    }

    public function testCreateClientCreatesTheAppCorrectly()
    {
        $initialState = $this->prophesize(AppStateInterface::class);
        $this->app->createInitialState()->willReturn($initialState);

        $this->provider->createClient(
            ['libraries' => [
                ['lib' => 1],
                ['lib' => 2]
            ]],
            ['app' => 'yep']
        );

        $this->combyna
            ->createEnvironment(
                ['libraries' => [
                    ['lib' => 1],
                    ['lib' => 2]
                ]]
            )
            ->shouldHaveBeenCalled();
        $this->combyna
            ->createApp(
                ['app' => 'yep'],
                $this->environmentNode
            )
            ->shouldHaveBeenCalled();
    }

    public function testGetContainerFetchesTheServiceContainer()
    {
        $serviceContainer = $this->prophesize(ContainerInterface::class);
        $this->combyna->getContainer()->willReturn($serviceContainer);

        static::assertSame($serviceContainer->reveal(), $this->provider->getContainer());
    }

    public function testUseProductionModeAsksCombynaToUseProduction()
    {
        $this->provider->useProductionMode();

        $this->combyna->useProductionMode()->shouldHaveBeenCalled();
    }
}
