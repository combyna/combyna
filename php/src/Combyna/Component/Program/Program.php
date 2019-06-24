<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\State\ProgramState;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\RouterInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use LogicException;

/**
 * Class Program
 *
 * A program is the internal representation of an app.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Program implements ProgramInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var OverlayViewCollectionInterface
     */
    private $overlayViewCollection;

    /**
     * @var PageViewCollectionInterface
     */
    private $pageViewCollection;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var EvaluationContextInterface
     */
    private $rootEvaluationContext;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @param EnvironmentInterface $environment
     * @param RouterInterface $router
     * @param ResourceRepositoryInterface $resourceRepository
     * @param PageViewCollectionInterface $pageViewCollection
     * @param OverlayViewCollectionInterface $overlayViewCollection
     * @param EvaluationContextInterface $rootEvaluationContext
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     */
    public function __construct(
        EnvironmentInterface $environment,
        RouterInterface $router,
        ResourceRepositoryInterface $resourceRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        EvaluationContextInterface $rootEvaluationContext,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
    ) {
        $this->environment = $environment;
        $this->overlayViewCollection = $overlayViewCollection;
        $this->pageViewCollection = $pageViewCollection;
        $this->resourceRepository = $resourceRepository;
        $this->rootEvaluationContext = $rootEvaluationContext;
        $this->router = $router;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRouteUrl($libraryName, $routeName, StaticBagInterface $argumentStaticBag)
    {
        $route = $this->resourceRepository->getRouteByName($libraryName, $routeName);

        return $route->generateUrl($argumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(RouterStateInterface $routerState)
    {
        return new ProgramState(
            $routerState,
            $this->pageViewCollection->createInitialState($routerState, $this->rootEvaluationContext),
            $this->overlayViewCollection->createInitialStates()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewByName($name)
    {
        if ($this->pageViewCollection->hasView($name)) {
            return $this->pageViewCollection->getView($name);
        }

        if ($this->overlayViewCollection->hasView($name)) {
            return $this->overlayViewCollection->getView($name);
        }

        throw new NotFoundException(sprintf('No page with name "%s" found', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEvaluationContext()
    {
        return $this->rootEvaluationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalDefinitionByName($libraryName, $signalName)
    {
        return $this->resourceRepository->getSignalDefinitionByName($libraryName, $signalName);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetByPath(array $names)
    {
        $libraryName = array_shift($names);
        $pathType = array_shift($names);

        if ($pathType === WidgetStatePathInterface::VIEW_PATH_TYPE) {
            if ($libraryName !== LibraryInterface::APP) {
                throw new LogicException(sprintf(
                    'Only apps can define views for now, ' .
                    'but tried to fetch view with name "%s" for library "%s"',
                    $names[0],
                    $libraryName
                ));
            }

            $widget = $this->pageViewCollection->getWidgetByPath($names);

            if ($widget !== null) {
                return $widget;
            }

            $widget = $this->overlayViewCollection->getWidgetByPath($names);

            if ($widget !== null) {
                return $widget;
            }
        } elseif ($pathType === WidgetStatePathInterface::WIDGET_DEFINITION_PATH_TYPE) {
            $widgetDefinitionName = array_shift($names);

            $widgetDefinition = $this->resourceRepository->getWidgetDefinitionByName(
                $libraryName,
                $widgetDefinitionName
            );

            if ($widgetDefinition instanceof PrimitiveWidgetDefinition) {
                throw new LogicException(sprintf(
                    'Primitives can define no widget tree, but [%s.%s.%s] was requested',
                    $libraryName,
                    $widgetDefinition,
                    implode('.', $names)
                ));
            }

            if ($widgetDefinition instanceof CompoundWidgetDefinition) {
                $rootWidgetName = array_shift($names);

                if ($rootWidgetName !== 'root') {
                    throw new LogicException(sprintf(
                        'Expected root widget for compound definition to be named "root" but it was "%s"',
                        $rootWidgetName
                    ));
                }

                return empty($names) ?
                    $widgetDefinition->getRootWidget() :
                    $widgetDefinition->getRootWidget()->getDescendantByPath($names);
            }
        } else {
            throw new LogicException(sprintf('Invalid path type "%s" given', $pathType));
        }

        throw new NotFoundException(sprintf(
            'No widget with path "%s" found',
            implode('-', $names)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName)
    {
        return $this->resourceRepository->getWidgetDefinitionByName($libraryName, $widgetDefinitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function handleSignal(
        ProgramStateInterface $programState,
        SignalInterface $signal
    ) {
        // TODO: Dispatch to all EntityStores
        // ...

        // Dispatch to all ViewStores and WidgetStores
        return $this->pageViewCollection->handleSignal($programState, $signal, $this, $this->environment);
    }

    /**
     * {@inheritdoc}
     */
    public function navigateTo(
        ProgramStateInterface $programState,
        $libraryName,
        $routeName,
        StaticBagInterface $routeArgumentBag
    ) {
        return $this->router->navigateTo(
            $programState,
            $this,
            $libraryName,
            $routeName,
            $routeArgumentBag,
            $this->pageViewCollection,
            $this->rootEvaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateUiState(ProgramStateInterface $programState)
    {
        // TODO: Overlay views

        $pageView = $this->pageViewCollection->getView($programState->getPageViewState()->getViewName());

        return $programState->withPageViewState(
            $pageView->reevaluateState(
                $programState->getPageViewState(),
                $this->rootEvaluationContext,
                $this->uiEvaluationContextFactory
            )
        );
    }
}
