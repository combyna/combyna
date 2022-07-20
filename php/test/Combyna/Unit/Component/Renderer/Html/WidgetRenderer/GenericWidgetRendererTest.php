<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Renderer\Html\WidgetRenderer;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\GenericNode;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Renderer\Html\UiRendererInterface;
use Combyna\Component\Renderer\Html\WidgetRenderer\GenericWidgetRenderer;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class GenericWidgetRendererTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericWidgetRendererTest extends TestCase
{
    /**
     * @var ObjectProphecy|StaticBagInterface
     */
    private $attributeStaticBag;

    /**
     * @var ObjectProphecy|ProgramInterface
     */
    private $program;

    /**
     * @var GenericWidgetRenderer
     */
    private $renderer;

    /**
     * @var ObjectProphecy|UiRendererInterface
     */
    private $uiRenderer;

    /**
     * @var ObjectProphecy|DefinedWidgetStateInterface
     */
    private $widgetState;

    /**
     * @var ObjectProphecy|WidgetStatePathInterface
     */
    private $widgetStatePath;

    public function setUp()
    {
        $this->attributeStaticBag = $this->prophesize(StaticBagInterface::class);
        $this->program = $this->prophesize(ProgramInterface::class);
        $this->uiRenderer = $this->prophesize(UiRendererInterface::class);
        $this->widgetState = $this->prophesize(DefinedWidgetStateInterface::class);
        $this->widgetStatePath = $this->prophesize(WidgetStatePathInterface::class);

        $this->attributeStaticBag->toNativeArray()
            ->willReturn([
                'first-attr' => 'first value',
                'second-attr' => 'second value'
            ]);

        $this->uiRenderer->renderTriggers($this->widgetStatePath->reveal(), $this->program->reveal())
            ->willReturn([
                ['library' => 'my_lib', 'event' => 'my_event']
            ]);

        $this->widgetState->getAttributeStaticBag()
            ->willReturn($this->attributeStaticBag->reveal());
        $this->widgetState->getWidgetDefinitionLibraryName()
            ->willReturn('my_lib');
        $this->widgetState->getWidgetDefinitionName()
            ->willReturn('my_widget');

        $this->widgetStatePath->getWidgetStatePath()
            ->willReturn(['path', 'to', 'my-widget']);

        $this->renderer = new GenericWidgetRenderer(
            $this->uiRenderer->reveal(),
            'my_lib',
            'my_widget'
        );
    }

    public function testGetWidgetDefinitionLibraryNameFetchesTheCorrectName()
    {
        static::assertSame('my_lib', $this->renderer->getWidgetDefinitionLibraryName());
    }

    public function testGetWidgetDefinitionNameFetchesTheCorrectName()
    {
        static::assertSame('my_widget', $this->renderer->getWidgetDefinitionName());
    }

    public function testRenderWidgetReturnsAValidGenericNodeWhenNoRootChildNameIsSet()
    {
        $node = $this->renderer->renderWidget(
            $this->widgetState->reveal(),
            $this->widgetStatePath->reveal(),
            $this->program->reveal()
        );

        static::assertInstanceOf(GenericNode::class, $node);
        static::assertEquals(
            [
                'type' => 'generic',
                'library' => 'my_lib',
                'widget' => 'my_widget',
                'path' => ['path', 'to', 'my-widget'],
                'attributes' => [
                    'first-attr' => 'first value',
                    'second-attr' => 'second value'
                ],
                'children' => [], // No root child name was set
                'triggers' => [
                    ['library' => 'my_lib', 'event' => 'my_event']
                ]
            ],
            $node->toArray()
        );
    }

    public function testRenderWidgetReturnsAValidGenericNodeWhenARootChildNameIsSet()
    {
        $this->renderer = new GenericWidgetRenderer(
            $this->uiRenderer->reveal(),
            'my_lib',
            'my_widget',
            'my-root-child'
        );
        $rootChildWidgetStatePath = $this->prophesize(WidgetStatePathInterface::class);
        $this->widgetStatePath->getChildStatePath('my-root-child')
            ->willReturn($rootChildWidgetStatePath->reveal());
        $rootChildRenderedNode = $this->prophesize(HtmlNodeInterface::class);
        $rootChildRenderedNode->toGenericArray()
            ->willReturn([
                ['type' => 'some-type', 'name' => 'my-root-child-node']
            ]);
        $this->uiRenderer->renderWidget($rootChildWidgetStatePath->reveal(), $this->program->reveal())
            ->willReturn($rootChildRenderedNode->reveal());

        $node = $this->renderer->renderWidget(
            $this->widgetState->reveal(),
            $this->widgetStatePath->reveal(),
            $this->program->reveal()
        );

        static::assertInstanceOf(GenericNode::class, $node);
        static::assertEquals(
            [
                'type' => 'generic',
                'library' => 'my_lib',
                'widget' => 'my_widget',
                'path' => ['path', 'to', 'my-widget'],
                'attributes' => [
                    'first-attr' => 'first value',
                    'second-attr' => 'second value'
                ],
                'children' => [
                    ['type' => 'some-type', 'name' => 'my-root-child-node']
                ],
                'triggers' => [
                    ['library' => 'my_lib', 'event' => 'my_event']
                ]
            ],
            $node->toArray()
        );
    }

    public function testRenderWidgetThrowsWhenAnInvalidTypeOfWidgetStateIsGiven()
    {
        $this->widgetState = $this->prophesize(WidgetStateInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Renderer must receive a my_lib.my_widget widget state'
        );

        $this->renderer->renderWidget(
            $this->widgetState->reveal(),
            $this->widgetStatePath->reveal(),
            $this->program->reveal()
        );
    }

    public function testRenderWidgetThrowsWhenADefinedWidgetStateOfWrongLibraryIsGiven()
    {
        $this->widgetState->getWidgetDefinitionLibraryName()
            ->willReturn('wrong_lib');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Renderer must receive a my_lib.my_widget widget state'
        );

        $this->renderer->renderWidget(
            $this->widgetState->reveal(),
            $this->widgetStatePath->reveal(),
            $this->program->reveal()
        );
    }

    public function testRenderWidgetThrowsWhenADefinedWidgetStateOfWrongDefinitionIsGiven()
    {
        $this->widgetState->getWidgetDefinitionName()
            ->willReturn('wrong_widget');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Renderer must receive a my_lib.my_widget widget state'
        );

        $this->renderer->renderWidget(
            $this->widgetState->reveal(),
            $this->widgetStatePath->reveal(),
            $this->program->reveal()
        );
    }
}
