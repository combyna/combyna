<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\App\State;

use Combyna\Component\App\State\AppState;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class AppStateTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppStateTest extends TestCase
{
    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var ObjectProphecy|ProgramStateInterface
     */
    private $programState;

    public function setUp()
    {
        $this->programState = $this->prophesize(ProgramStateInterface::class);

        $this->appState = new AppState($this->programState->reveal());
    }

    public function testGetPageTitleGetsTitleFromVisibleViewState()
    {
        $pageViewState = $this->prophesize(PageViewStateInterface::class);
        $pageViewState->getTitle()->willReturn('My page title');
        $this->programState->getPageViewState()->willReturn($pageViewState);

        static::assertSame('My page title', $this->appState->getPageTitle());
    }

    public function testGetWidgetStatePathByTagDelegatesToTheProgramState()
    {
        $widgetStatePath = $this->prophesize(WidgetStatePathInterface::class);
        $this->programState->getWidgetStatePathByTag('my_tag')
            ->willReturn($widgetStatePath);

        static::assertSame($widgetStatePath->reveal(), $this->appState->getWidgetStatePathByTag('my_tag'));
    }

    public function testGetWidgetStatePathsByTagDelegatesToTheProgramState()
    {
        $widgetStatePath1 = $this->prophesize(WidgetStatePathInterface::class);
        $widgetStatePath2 = $this->prophesize(WidgetStatePathInterface::class);
        $this->programState->getWidgetStatePathsByTag('my_tag')
            ->willReturn([
                $widgetStatePath1,
                $widgetStatePath2
            ]);

        static::assertSame(
            [
                $widgetStatePath1->reveal(),
                $widgetStatePath2->reveal()
            ],
            $this->appState->getWidgetStatePathsByTag('my_tag')
        );
    }

    public function testWithProgramStateReturnsANewAppStateWhenProgramStateDiffers()
    {
        $differentProgramState = $this->prophesize(ProgramStateInterface::class);

        $resultingAppState = $this->appState->withProgramState($differentProgramState->reveal());

        static::assertInstanceOf(AppStateInterface::class, $resultingAppState);
        static::assertNotSame($this->appState, $resultingAppState);
        static::assertSame($differentProgramState->reveal(), $resultingAppState->getProgramState());
    }

    public function testWithProgramStateReturnsTheSameAppStateWhenProgramStateIsIdentical()
    {
        $resultingAppState = $this->appState->withProgramState($this->programState->reveal());

        static::assertSame($this->appState, $resultingAppState);
    }
}
