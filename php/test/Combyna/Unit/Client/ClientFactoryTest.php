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
use Combyna\Client\ClientFactory;
use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Component\Ui\Environment\Library\GenericWidgetValueProviderInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ClientFactoryTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ClientFactoryTest extends TestCase
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
     * @var ClientFactory
     */
    private $factory;

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
        $this->combyna->useProductionMode()->willReturn();

        $this->factory = new ClientFactory(
            $this->combyna->reveal(),
            $this->arrayRenderer->reveal(),
            $this->widgetValueProvider->reveal()
        );
    }

    public function testAddWidgetValueProviderDelegatesToTheGenericProvider()
    {
        $myCallable = function () {};

        $this->factory->addWidgetValueProvider(
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
        $client = $this->factory->createClient(
            ['libraries' => [
                ['lib' => 1],
                ['lib' => 2]
            ]],
            ['app' => 'yep']
        );

        $this->assert($client)->isAnInstanceOf(Client::class);
    }

    public function testCreateClientCreatesTheAppCorrectly()
    {
        $this->factory->createClient(
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

        $this->assert($this->factory->getContainer())->exactlyEquals($serviceContainer->reveal());
    }

    public function testUseProductionModeAsksCombynaToUseProduction()
    {
        $this->factory->useProductionMode();

        $this->combyna->useProductionMode()->shouldHaveBeenCalled();
    }
}
