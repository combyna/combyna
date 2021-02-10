<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\App\HomeInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\EventDispatcher\Event\RouteNavigatedEvent;
use Combyna\Component\Router\State\RouterState;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Router
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Router implements RouterInterface
{
    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var HomeInterface
     */
    private $home;

    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    /**
     * @var SignalDefinitionRepositoryInterface
     */
    private $signalDefinitionRepository;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param DispatcherInterface $dispatcher
     * @param RouteRepositoryInterface $routeRepository
     * @param HomeInterface $home
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DispatcherInterface $dispatcher,
        RouteRepositoryInterface $routeRepository,
        HomeInterface $home,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository
    ) {
        $this->dispatcher = $dispatcher;
        $this->eventDispatcher = $eventDispatcher;
        $this->home = $home;
        $this->routeRepository = $routeRepository;
        $this->signalDefinitionRepository = $signalDefinitionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(EvaluationContextInterface $evaluationContext)
    {
        $homeRouteArgumentStaticBag = $this->home->argumentExpressionBagToStaticBag($evaluationContext);

        return new RouterState($this->home->getRoute(), $homeRouteArgumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function navigateTo(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        $libraryName,
        $routeName,
        StaticBagInterface $routeArgumentBag,
        PageViewCollectionInterface $pageViewCollection,
        EvaluationContextInterface $evaluationContext
    ) {
        $oldRouterState = $programState->getRouterState();

        $route = $this->routeRepository->getByName($libraryName, $routeName);
        $route->assertValidArgumentBag($routeArgumentBag);

        $newRouterState = $oldRouterState->withRoute($route, $routeArgumentBag);

        if ($newRouterState === $oldRouterState) {
            // No new route has been navigated to: nothing to do
            return $programState;
        }

        // We're on a new page via a new route, so create an initial state for its view
        $newPageViewState = $pageViewCollection->createInitialState($newRouterState, $evaluationContext);

        // After navigating to the new page, we now have a new state for the router (its new route and args)
        // and a corresponding new view state, so we can create a new program state
        $newProgramStateAfterNavigation = $programState->withPage($newRouterState, $newPageViewState);

        $navigatedSignalDefinition = $this->signalDefinitionRepository->getByName(
            LibraryInterface::CORE,
            'navigated'
        );
        $payloadStaticBag = $navigatedSignalDefinition->getPayloadStaticBagModel()
            ->coerceNativeArrayToBag(
                [
                    'library' => $libraryName,
                    'route' => $routeName
                ],
                $evaluationContext
            );

        $newProgramStateAfterDispatch = $this->dispatcher->dispatchSignal(
            $program,
            $newProgramStateAfterNavigation,
            $navigatedSignalDefinition,
            $payloadStaticBag
        );

        // Dispatch an event to provide an extension point for updating the address bar with pushState, etc.
        $this->eventDispatcher->dispatch(
            RouterEvents::ROUTE_NAVIGATED,
            new RouteNavigatedEvent(
                $newRouterState
            )
        );

        return $newProgramStateAfterDispatch;
    }
}
