<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Plugin;

use Combyna\CombynaBootstrap;
use Combyna\Component\Common\ComponentExtensionInterface;
use Combyna\Component\Framework\Originators;
use Combyna\Component\Plugin\PluginInterface;
use Combyna\Component\Plugin\SubPluginInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginIntegratedTest
 *
 * Tests the plugins and sub-plugins features
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PluginIntegratedTest extends TestCase
{
    /**
     * @var ObjectProphecy|PluginInterface
     */
    private $plugin;

    public function setUp()
    {
        $this->plugin = $this->prophesize(PluginInterface::class);
        /** @var ObjectProphecy|ComponentExtensionInterface $pluginExtension */
        $pluginExtension = $this->prophesize(ComponentExtensionInterface::class);
        $pluginExtension->getAlias()->willReturn('Test');
        $pluginExtension->getNamespace()->willReturn('ns');
        $pluginExtension->load(Argument::type('array'), Argument::type(ContainerBuilder::class))
            ->will(function (array $args) {
                $containerBuilder = $args[1];
                $containerBuilder->setParameter('my-test-parameter-from-extension', 'my test value from extension');
            });
        $this->plugin->build(Argument::type(ContainerBuilder::class))->willReturn(null);
        $this->plugin->getContainerExtension()->willReturn($pluginExtension->reveal());
        $this->plugin->getSubPlugins()->willReturn([]);
        $this->plugin->process(Argument::type(ContainerBuilder::class))
            ->will(function (array $args) {
                $containerBuilder = $args[0];
                $containerBuilder->setParameter('my-test-parameter-from-compiler-pass', 'my test value from compiler pass');
            });
    }

    public function testPluginIsAbleToModifyTheContainer()
    {
        $combynaBootstrap = new CombynaBootstrap([
            $this->plugin->reveal()
        ]);

        $container = $combynaBootstrap->getContainerBuilder();

        static::assertInstanceOf(ContainerInterface::class, $container);
        static::assertSame('my test value from extension', $container->getParameter('my-test-parameter-from-extension'));
        static::assertSame('my test value from compiler pass', $container->getParameter('my-test-parameter-from-compiler-pass'));
    }

    public function testSubPluginsAreLoadedForASupportedOriginator()
    {
        /** @var ObjectProphecy|SubPluginInterface $subPlugin */
        $subPlugin = $this->prophesize(SubPluginInterface::class);
        /** @var ObjectProphecy|ComponentExtensionInterface $subPluginExtension */
        $subPluginExtension = $this->prophesize(ComponentExtensionInterface::class);
        $subPluginExtension->getAlias()->willReturn('Test');
        $subPluginExtension->getNamespace()->willReturn('ns');
        $subPluginExtension->load(Argument::type('array'), Argument::type(ContainerBuilder::class))
            ->willReturn(null);
        $subPlugin->build(Argument::type(ContainerBuilder::class))->willReturn(null);
        $subPlugin->getContainerExtension()->willReturn($subPluginExtension->reveal());
        $subPlugin->getSubPlugins()->willReturn([]);
        $subPlugin->getSupportedOriginators()->willReturn([Originators::CLIENT]);
        $subPlugin->process(Argument::type(ContainerBuilder::class))
            ->will(function (array $args) {
                $containerBuilder = $args[0];
                $containerBuilder->setParameter('my-test-parameter-from-sub-plugin-compiler-pass', 'my test value from sub-plugin compiler pass');
            });
        $this->plugin->getSubPlugins()->willReturn([$subPlugin->reveal()]);

        $combynaBootstrap = new CombynaBootstrap([
            $this->plugin->reveal()
        ], Originators::CLIENT);

        $container = $combynaBootstrap->getContainerBuilder();

        static::assertInstanceOf(ContainerInterface::class, $container);
        static::assertSame('my test value from sub-plugin compiler pass', $container->getParameter('my-test-parameter-from-sub-plugin-compiler-pass'));
    }

    public function testSubPluginsAreNotLoadedForAnUnsupportedOriginator()
    {
        /** @var ObjectProphecy|SubPluginInterface $subPlugin */
        $subPlugin = $this->prophesize(SubPluginInterface::class);
        /** @var ObjectProphecy|ComponentExtensionInterface $subPluginExtension */
        $subPluginExtension = $this->prophesize(ComponentExtensionInterface::class);
        $subPluginExtension->getAlias()->willReturn('Test');
        $subPluginExtension->getNamespace()->willReturn('ns');
        $subPluginExtension->load(Argument::type('array'), Argument::type(ContainerBuilder::class))
            ->willReturn(null);
        $subPlugin->build(Argument::type(ContainerBuilder::class))->willReturn(null);
        $subPlugin->getContainerExtension()->willReturn($subPluginExtension->reveal());
        $subPlugin->getSubPlugins()->willReturn([]);
        $subPlugin->getSupportedOriginators()->willReturn([Originators::CLIENT]);
        $subPlugin->process(Argument::type(ContainerBuilder::class))
            ->will(function (array $args) {
                $containerBuilder = $args[0];
                $containerBuilder->setParameter('my-test-parameter-from-sub-plugin-compiler-pass', 'my test value from sub-plugin compiler pass');
            });
        $this->plugin->getSubPlugins()->willReturn([$subPlugin->reveal()]);

        $combynaBootstrap = new CombynaBootstrap([
            $this->plugin->reveal()
        ], Originators::SERVER); // Not the originator that the sub-plugin supports

        $container = $combynaBootstrap->getContainerBuilder();

        static::assertInstanceOf(ContainerInterface::class, $container);
        static::assertFalse($container->hasParameter('my-test-parameter-from-sub-plugin-compiler-pass'));
    }
}
