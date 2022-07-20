<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Ui\State\Widget;

use Combyna\Component\State\Exception\AncestorStateUnavailableException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePath;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class WidgetStatePathTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetStatePathTest extends TestCase
{
    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $currentState;

    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $grandparentState;

    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $parentState;

    /**
     * @var WidgetStatePath
     */
    private $path;

    /**
     * @var ObjectProphecy|UiStateFactoryInterface
     */
    private $stateFactory;

    public function setUp()
    {
        $this->currentState = $this->prophesize(WidgetStateInterface::class);
        $this->grandparentState = $this->prophesize(WidgetStateInterface::class);
        $this->parentState = $this->prophesize(WidgetStateInterface::class);
        $this->stateFactory = $this->prophesize(UiStateFactoryInterface::class);

        $this->grandparentState->getStateName()
            ->willReturn('grandparent');
        $this->grandparentState->getType()
            ->willReturn('grandparent-type');
        $this->grandparentState->getWidgetPath()
            ->willReturn(['root']);
        $this->parentState->getStateName()
            ->willReturn('parent');
        $this->parentState->getType()
            ->willReturn('parent-type');
        $this->parentState->getWidgetPath()
            ->willReturn(['root', 'my_parent_widget']);
        $this->currentState->getStateName()
            ->willReturn('current');
        $this->currentState->getType()
            ->willReturn('current-type');
        $this->currentState->getWidgetPath()
            ->willReturn(['root', 'my_parent_widget', 'my_widget']);

        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $this->grandparentState->reveal(),
            $this->parentState->reveal(),
            $this->currentState->reveal()
        ]);
    }

    public function testGetParentStateReturnsTheParentStateWhenAvailable()
    {
        static::assertSame($this->parentState->reveal(), $this->path->getParentState());
    }

    public function testGetParentStateThrowsExceptionWhenUnavailable()
    {
        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $this->currentState->reveal()
        ]);

        $this->expectException(AncestorStateUnavailableException::class);
        $this->expectExceptionMessage(
            'Parent state unavailable'
        );

        $this->path->getParentState();
    }

    public function testGetParentStateTypeReturnsTheTypeOfTheParentStateWhenAvailable()
    {
        static::assertSame('parent-type', $this->path->getParentStateType());
    }

    public function testGetParentStateTypeThrowsExceptionWhenUnavailable()
    {
        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $this->currentState->reveal()
        ]);

        $this->expectException(AncestorStateUnavailableException::class);
        $this->expectExceptionMessage(
            'Parent state unavailable'
        );

        $this->path->getParentStateType();
    }

    public function testGetWidgetPathReturnsCorrectPathWhenNoCompoundWidgetIsInvolved()
    {
        static::assertEquals(
            [
                'app',
                'view',
                'grandparent',
                'root',
                'my_parent_widget',
                'my_widget'
            ],
            $this->path->getWidgetPath()
        );
    }

    public function testGetWidgetPathReturnsCorrectPathWhenWidgetIsInsideCompoundWidgetDefinitionRoot()
    {
        $compoundWidgetState = $this->prophesize(DefinedCompoundWidgetStateInterface::class);
        $compoundWidgetState->getWidgetDefinitionLibraryName()
            ->willReturn('my_lib');
        $compoundWidgetState->getWidgetDefinitionName()
            ->willReturn('my_compound_widget_def');
        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $this->grandparentState->reveal(),
            $compoundWidgetState->reveal(),
            $this->parentState->reveal(),
            $this->currentState->reveal()
        ]);

        static::assertEquals(
            [
                'my_lib', // Widget is inside a compound widget definition defined by this library
                'widget',
                'my_compound_widget_def',
                'root',
                'my_parent_widget',
                'my_widget'
            ],
            $this->path->getWidgetPath()
        );
    }

    public function testGetWidgetPathReturnsCorrectPathWhenWidgetIsInsideAChildOfCompoundWidget()
    {
        $compoundWidgetState = $this->prophesize(DefinedCompoundWidgetStateInterface::class);
        $compoundWidgetState->getWidgetDefinitionLibraryName()
            ->willReturn('my_lib');
        $compoundWidgetState->getWidgetDefinitionName()
            ->willReturn('my_compound_widget_def');
        $childReferenceWidgetState = $this->prophesize(ChildReferenceWidgetStateInterface::class);
        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $this->grandparentState->reveal(),
            $compoundWidgetState->reveal(),
            $childReferenceWidgetState->reveal(),
            $this->parentState->reveal(),
            $this->currentState->reveal()
        ]);

        static::assertEquals(
            [
                'app', // Widget is not inside the definition so path should point to its place in the view
                'view',
                'grandparent',
                'root',
                'my_parent_widget',
                'my_widget'
            ],
            $this->path->getWidgetPath()
        );
    }

    public function testGetWidgetPathReturnsCorrectPathWhenWidgetIsInsideATreeInsideAChildOfCompoundWidget()
    {
        $pageViewState = $this->prophesize(PageViewStateInterface::class);
        $pageViewState->getStateName()
            ->willReturn('my_view');
        $compoundWidgetState = $this->prophesize(DefinedCompoundWidgetStateInterface::class);
        $compoundWidgetState->getStateName()
            ->willReturn('root');
        $compoundWidgetState->getWidgetDefinitionLibraryName()
            ->willReturn('my_lib');
        $compoundWidgetState->getWidgetDefinitionName()
            ->willReturn('my_compound_widget_def');
        $primitiveWidgetState = $this->prophesize(DefinedPrimitiveWidgetStateInterface::class);
        $primitiveWidgetState->getStateName()
            ->willReturn('child_for_compound_widget');
        $primitiveWidgetState->getWidgetDefinitionLibraryName()
            ->willReturn('my_lib');
        $primitiveWidgetState->getWidgetDefinitionName()
            ->willReturn('my_primitive_widget_def');
        $childReferenceWidgetState = $this->prophesize(ChildReferenceWidgetStateInterface::class);
        $innermostPrimitiveWidgetState = $this->prophesize(DefinedPrimitiveWidgetStateInterface::class);
        $innermostPrimitiveWidgetState->getWidgetPath()
            ->willReturn(['root', 'my_innermost_primitive_widget']);
        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $pageViewState->reveal(),
            $compoundWidgetState->reveal(),
            $primitiveWidgetState->reveal(),
            $childReferenceWidgetState->reveal(),
            $innermostPrimitiveWidgetState->reveal()
        ]);

        static::assertEquals(
            [
                'app', // Widget is not inside the definition so path should point to its place in the view
                'view',
                'my_view',
                'root', // The compound widget

                // Note that the state for the child reference widget and the primitive widget it is inside
                // are both excluded from the widget path as those are part of the compound widget's inner root tree

                'my_innermost_primitive_widget'
            ],
            $this->path->getWidgetPath()
        );
    }

    public function testGetWidgetStatePathReturnsAListOfAllStateNames()
    {
        static::assertEquals(
            [
                'grandparent',
                'parent',
                'current'
            ],
            $this->path->getWidgetStatePath()
        );
    }

    public function testHasParentReturnsTrueWhenAtLeastAParentStateIsAvailable()
    {
        static::assertTrue($this->path->hasParent());
    }

    public function testHasParentReturnsFalseWhenNoParentStateIsAvailable()
    {
        $this->path = new WidgetStatePath($this->stateFactory->reveal(), [
            $this->currentState->reveal()
        ]);

        static::assertFalse($this->path->hasParent());
    }
}
