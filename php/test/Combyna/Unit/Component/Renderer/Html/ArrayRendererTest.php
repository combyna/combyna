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

use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ArrayRendererTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArrayRendererTest extends TestCase
{
    /**
     * @var ObjectProphecy|AppStateInterface
     */
    private $appState;

    /**
     * @var ObjectProphecy|WidgetStatePathInterface
     */
    private $pathToStateOfViewState1RootWidget;

    /**
     * @var ObjectProphecy|WidgetStatePathInterface
     */
    private $pathToStateOfViewState2RootWidget;

    /**
     * @var ObjectProphecy|ProgramInterface
     */
    private $program;

    /**
     * @var ObjectProphecy|HtmlNodeInterface
     */
    private $renderedViewState1RootWidget;

    /**
     * @var ObjectProphecy|HtmlNodeInterface
     */
    private $renderedViewState2RootWidget;

    /**
     * @var ArrayRenderer
     */
    private $renderer;

    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $stateOfViewState1RootWidget;

    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $stateOfViewState2RootWidget;

    /**
     * @var ObjectProphecy|UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ObjectProphecy|ViewStateInterface
     */
    private $viewState1;

    /**
     * @var ObjectProphecy|ViewStateInterface
     */
    private $viewState2;

    /**
     * @var ObjectProphecy|DelegatingWidgetRenderer
     */
    private $widgetRenderer;

    public function setUp()
    {
        $this->appState = $this->prophesize(AppStateInterface::class);
        $this->pathToStateOfViewState1RootWidget = $this->prophesize(WidgetStatePathInterface::class);
        $this->pathToStateOfViewState2RootWidget = $this->prophesize(WidgetStatePathInterface::class);
        $this->program = $this->prophesize(ProgramInterface::class);
        $this->renderedViewState1RootWidget = $this->prophesize(HtmlNodeInterface::class);
        $this->renderedViewState2RootWidget = $this->prophesize(HtmlNodeInterface::class);
        $this->stateOfViewState1RootWidget = $this->prophesize(WidgetStateInterface::class);
        $this->stateOfViewState2RootWidget = $this->prophesize(WidgetStateInterface::class);
        $this->uiStateFactory = $this->prophesize(UiStateFactoryInterface::class);
        $this->viewState1 = $this->prophesize(ViewStateInterface::class);
        $this->viewState2 = $this->prophesize(ViewStateInterface::class);
        $this->widgetRenderer = $this->prophesize(DelegatingWidgetRenderer::class);

        $this->appState->getVisibleViewStates()
            ->willReturn([
                $this->viewState1->reveal(),
                $this->viewState2->reveal()
            ]);

        $this->renderedViewState1RootWidget->toArray()
            ->willReturn(['my' => 'rendered first view state root widget']);
        $this->renderedViewState2RootWidget->toArray()
            ->willReturn(['my' => 'rendered second view state root widget']);

        $this->uiStateFactory
            ->createWidgetStatePath([
                $this->viewState1->reveal(),
                $this->stateOfViewState1RootWidget->reveal()
            ])
            ->willReturn($this->pathToStateOfViewState1RootWidget->reveal());
        $this->uiStateFactory
            ->createWidgetStatePath([
                $this->viewState2->reveal(),
                $this->stateOfViewState2RootWidget->reveal()
            ])
            ->willReturn($this->pathToStateOfViewState2RootWidget->reveal());

        $this->viewState1->getRootWidgetState()
            ->willReturn($this->stateOfViewState1RootWidget->reveal());
        $this->viewState1->getType()
            ->willReturn('my-first-type');
        $this->viewState1->getViewName()
            ->willReturn('my-first-view');
        $this->viewState2->getRootWidgetState()
            ->willReturn($this->stateOfViewState2RootWidget->reveal());
        $this->viewState2->getType()
            ->willReturn('my-second-type');
        $this->viewState2->getViewName()
            ->willReturn('my-second-view');

        $this->widgetRenderer->renderWidget($this->pathToStateOfViewState1RootWidget->reveal(), $this->program->reveal())
            ->willReturn($this->renderedViewState1RootWidget->reveal());
        $this->widgetRenderer->renderWidget($this->pathToStateOfViewState2RootWidget->reveal(), $this->program->reveal())
            ->willReturn($this->renderedViewState2RootWidget->reveal());

        $this->renderer = new ArrayRenderer($this->widgetRenderer->reveal(), $this->uiStateFactory->reveal());
    }

    public function testRenderViewsReturnsCorrectArrayStructure()
    {
        self::assertEquals(
            [
                [
                    'type' => 'my-first-type',
                    'view-name' => 'my-first-view',
                    'widget' => ['my' => 'rendered first view state root widget']
                ],
                [
                    'type' => 'my-second-type',
                    'view-name' => 'my-second-view',
                    'widget' => ['my' => 'rendered second view state root widget']
                ]
            ],
            $this->renderer->renderViews($this->appState->reveal(), $this->program->reveal())
        );
    }
}
