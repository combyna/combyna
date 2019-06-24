<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Program;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\Program;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Router\RouterInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ProgramTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProgramTest extends TestCase
{
    /**
     * @var ObjectProphecy|EnvironmentInterface
     */
    private $environment;

    /**
     * @var ObjectProphecy|OverlayViewCollectionInterface
     */
    private $overlayViewCollection;

    /**
     * @var ObjectProphecy|PageViewCollectionInterface
     */
    private $pageViewCollection;

    /**
     * @var Program
     */
    private $program;

    /**
     * @var ObjectProphecy|ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $rootEvaluationContext;

    /**
     * @var ObjectProphecy|RouterInterface
     */
    private $router;

    /**
     * @var ObjectProphecy|UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var ObjectProphecy|CompoundWidgetDefinition
     */
    private $widgetDefinition;

    /**
     * @var ObjectProphecy|WidgetInterface
     */
    private $widgetDefinitionRoot;

    /**
     * @var ObjectProphecy|WidgetInterface
     */
    private $widgetInsideDefinitionRoot;

    /**
     * @var ObjectProphecy|WidgetInterface
     */
    private $widgetInsideViewRoot;

    public function setUp()
    {
        $this->environment = $this->prophesize(EnvironmentInterface::class);
        $this->overlayViewCollection = $this->prophesize(OverlayViewCollectionInterface::class);
        $this->pageViewCollection = $this->prophesize(PageViewCollectionInterface::class);
        $this->resourceRepository = $this->prophesize(ResourceRepositoryInterface::class);
        $this->rootEvaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->router = $this->prophesize(RouterInterface::class);
        $this->uiEvaluationContextFactory = $this->prophesize(UiEvaluationContextFactoryInterface::class);
        $this->widgetDefinition = $this->prophesize(CompoundWidgetDefinition::class);
        $this->widgetDefinitionRoot = $this->prophesize(WidgetInterface::class);
        $this->widgetInsideDefinitionRoot = $this->prophesize(WidgetInterface::class);
        $this->widgetInsideViewRoot = $this->prophesize(WidgetInterface::class);

        $this->pageViewCollection->getWidgetByPath(['my_view', 'root', 'my_widget'])
            ->willReturn($this->widgetInsideViewRoot);

        $this->resourceRepository->getWidgetDefinitionByName('my_lib', 'my_widget_def')
            ->willReturn($this->widgetDefinition);
        $this->widgetDefinition->getRootWidget()
            ->willReturn($this->widgetDefinitionRoot);
        $this->widgetDefinitionRoot->getDescendantByPath(['your_widget', 'my_widget'])
            ->willReturn($this->widgetInsideDefinitionRoot);

        $this->program = new Program(
            $this->environment->reveal(),
            $this->router->reveal(),
            $this->resourceRepository->reveal(),
            $this->pageViewCollection->reveal(),
            $this->overlayViewCollection->reveal(),
            $this->rootEvaluationContext->reveal(),
            $this->uiEvaluationContextFactory->reveal()
        );
    }

    public function testGetWidgetByPathThrowsExceptionWhenTryingToFetchAViewFromALibrary()
    {
        $this->setExpectedException(
            LogicException::class,
            'Only apps can define views for now, but tried to fetch view with name "my_view" for library "my_lib"'
        );

        $this->program->getWidgetByPath(['my_lib', WidgetStatePathInterface::VIEW_PATH_TYPE, 'my_view']);
    }

    public function testGetWidgetByPathCanFetchWidgetsInsideAPageViewRoot()
    {
        self::assertSame(
            $this->widgetInsideViewRoot->reveal(),
            $this->program->getWidgetByPath(
                ['app', WidgetStatePathInterface::VIEW_PATH_TYPE, 'my_view', 'root', 'my_widget']
            )
        );
    }

    public function testGetWidgetByPathCanFetchWidgetsInsideAWidgetDefinitionRoot()
    {
        self::assertSame(
            $this->widgetInsideDefinitionRoot->reveal(),
            $this->program->getWidgetByPath([
                'my_lib',
                WidgetStatePathInterface::WIDGET_DEFINITION_PATH_TYPE,
                'my_widget_def',
                'root',
                'your_widget',
                'my_widget'
            ])
        );
    }

    public function testGetWidgetByPathThrowsExceptionWhenCompoundWidgetDefinitionPathUsesWrongRootName()
    {
        $this->setExpectedException(
            LogicException::class,
            'Expected root widget for compound definition to be named "root" but it was "not_a_valid_root_name"'
        );

        $this->program->getWidgetByPath([
            'my_lib',
            WidgetStatePathInterface::WIDGET_DEFINITION_PATH_TYPE,
            'my_widget_def',
            'not_a_valid_root_name',
            'inner1',
            'inner2'
        ]);
    }

    public function testGetWidgetByPathThrowsExceptionForInvalidPathType()
    {
        $this->setExpectedException(
            LogicException::class,
            'Invalid path type "my_invalid_path_type" given'
        );

        $this->program->getWidgetByPath(['my_lib', 'my_invalid_path_type', 'inner1', 'inner2']);
    }
}
