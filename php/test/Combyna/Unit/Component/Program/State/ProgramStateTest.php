<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Program\State;

use Combyna\Component\Program\State\ProgramState;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ProgramStateTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProgramStateTest extends TestCase
{
    /**
     * @var ObjectProphecy|PageViewStateInterface
     */
    private $pageViewState;

    /**
     * @var ProgramStateInterface
     */
    private $programState;

    /**
     * @var ObjectProphecy|RouterStateInterface
     */
    private $routerState;

    /**
     * @var ObjectProphecy|ViewStateInterface
     */
    private $visibleOverlayViewState1;

    /**
     * @var ObjectProphecy|ViewStateInterface
     */
    private $visibleOverlayViewState2;

    public function setUp()
    {
        $this->pageViewState = $this->prophesize(PageViewStateInterface::class);
        $this->programState = $this->prophesize(ProgramStateInterface::class);
        $this->routerState = $this->prophesize(RouterStateInterface::class);
        $this->visibleOverlayViewState1 = $this->prophesize(ViewStateInterface::class);
        $this->visibleOverlayViewState2 = $this->prophesize(ViewStateInterface::class);

        $this->pageViewState->getWidgetStatePathsByTag(Argument::any())
            ->willReturn([]);
        $this->visibleOverlayViewState1->getWidgetStatePathsByTag(Argument::any())
            ->willReturn([]);
        $this->visibleOverlayViewState2->getWidgetStatePathsByTag(Argument::any())
            ->willReturn([]);

        $this->programState = new ProgramState(
            $this->routerState->reveal(),
            $this->pageViewState->reveal(),
            [
                $this->visibleOverlayViewState1->reveal(),
                $this->visibleOverlayViewState2->reveal()
            ]
        );
    }

    public function testGetWidgetStatePathByTagFetchesAMatchingPathFromThePageViewState()
    {
        $widgetStatePath = $this->prophesize(WidgetStatePathInterface::class);
        $this->pageViewState->getWidgetStatePathsByTag('my_tag')
            ->willReturn([$widgetStatePath]);

        $this->assert($this->programState->getWidgetStatePathByTag('my_tag'))
            ->exactlyEquals($widgetStatePath->reveal());
    }

    public function testGetWidgetStatePathsByTagFetchesMatchingPathsFromThePageViewState()
    {
        $widgetStatePath1 = $this->prophesize(WidgetStatePathInterface::class);
        $widgetStatePath2 = $this->prophesize(WidgetStatePathInterface::class);
        $this->pageViewState->getWidgetStatePathsByTag('my_tag')
            ->willReturn([$widgetStatePath1, $widgetStatePath2]);

        $this->assert($this->programState->getWidgetStatePathsByTag('my_tag'))
            ->exactlyEquals([
                $widgetStatePath1->reveal(),
                $widgetStatePath2->reveal()
            ]);
    }

    public function testGetWidgetStatePathByTagFetchesAMatchingPathFromTheSecondOverlayViewState()
    {
        $widgetStatePath = $this->prophesize(WidgetStatePathInterface::class);
        $this->visibleOverlayViewState2->getWidgetStatePathsByTag('my_tag')
            ->willReturn([$widgetStatePath]);

        $this->assert($this->programState->getWidgetStatePathByTag('my_tag'))
            ->exactlyEquals($widgetStatePath->reveal());
    }

    public function testGetWidgetStatePathsByTagFetchesMatchingPathsFromTheSecondOverlayViewState()
    {
        $widgetStatePath1 = $this->prophesize(WidgetStatePathInterface::class);
        $widgetStatePath2 = $this->prophesize(WidgetStatePathInterface::class);
        $this->visibleOverlayViewState2->getWidgetStatePathsByTag('my_tag')
            ->willReturn([$widgetStatePath1, $widgetStatePath2]);

        $this->assert($this->programState->getWidgetStatePathsByTag('my_tag'))
            ->exactlyEquals([
                $widgetStatePath1->reveal(),
                $widgetStatePath2->reveal()
            ]);
    }

    public function testWithPageViewStateReturnsANewProgramStateWhenPageViewStateDiffers()
    {
        $differentPageViewState = $this->prophesize(PageViewStateInterface::class);

        $resultingProgramState = $this->programState->withPageViewState($differentPageViewState->reveal());

        $this->assert($resultingProgramState)->isAnInstanceOf(ProgramStateInterface::class);
        $this->assert($resultingProgramState)->doesNotExactlyEqual($this->programState);
        $this->assert($resultingProgramState->getPageViewState())->exactlyEquals($differentPageViewState->reveal());
    }

    public function testWithPageViewStateReturnsTheSameProgramStateWhenPageViewStateIsIdentical()
    {
        $resultingProgramState = $this->programState->withPageViewState($this->pageViewState->reveal());

        $this->assert($resultingProgramState)->exactlyEquals($this->programState);
    }
}
