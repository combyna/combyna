<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Renderer\Html;

use Combyna\Component\Renderer\Html\GenericNode;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class GenericNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $widgetState;

    public function setUp()
    {
        $this->widgetState = $this->prophesize(WidgetStateInterface::class);

        $this->widgetState->getWidgetDefinitionLibraryName()
            ->willReturn('some_lib');
        $this->widgetState->getWidgetDefinitionName()
            ->willReturn('some_widget');
    }

    public function testToArrayReturnsCorrectArrayStructureWhenRootChildNodeIsGiven()
    {
        $rootChildNode = $this->prophesize(HtmlNodeInterface::class);
        $rootChildNode->toGenericArray()
            ->willReturn([
                ['type' => 'some-type', 'name' => 'my-child-node'],
                ['type' => 'some-other-type', 'name' => 'my-other-child-node']
            ]);
        $node = new GenericNode(
            $this->widgetState->reveal(),
            ['path', 'to', 'my-widget'],
            ['first-attr' => 'first value', 'second-attr' => 'second value'],
            [
                ['library' => 'my_lib', 'event' => 'my_event'],
                ['library' => 'your_lib', 'event' => 'your_event']
            ],
            $rootChildNode->reveal()
        );

        static::assertEquals(
            [
                'type' => 'generic',
                'library' => 'some_lib',
                'widget' => 'some_widget',
                'path' => ['path', 'to', 'my-widget'],
                'attributes' => ['first-attr' => 'first value', 'second-attr' => 'second value'],
                'children' => [
                    ['type' => 'some-type', 'name' => 'my-child-node'],
                    ['type' => 'some-other-type', 'name' => 'my-other-child-node']
                ],
                'triggers' => [
                    ['library' => 'my_lib', 'event' => 'my_event'],
                    ['library' => 'your_lib', 'event' => 'your_event']
                ]
            ],
            $node->toArray()
        );
    }

    public function testToArrayReturnsCorrectArrayStructureWhenRootChildNodeIsNotGiven()
    {
        $node = new GenericNode(
            $this->widgetState->reveal(),
            ['path', 'to', 'my-widget'],
            ['first-attr' => 'first value', 'second-attr' => 'second value'],
            [
                ['library' => 'my_lib', 'event' => 'my_event'],
                ['library' => 'your_lib', 'event' => 'your_event']
            ],
            null
        );

        static::assertEquals(
            [
                'type' => 'generic',
                'library' => 'some_lib',
                'widget' => 'some_widget',
                'path' => ['path', 'to', 'my-widget'],
                'attributes' => ['first-attr' => 'first value', 'second-attr' => 'second value'],
                'children' => [], // No root child node was given
                'triggers' => [
                    ['library' => 'my_lib', 'event' => 'my_event'],
                    ['library' => 'your_lib', 'event' => 'your_event']
                ]
            ],
            $node->toArray()
        );
    }
}
