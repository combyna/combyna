<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\App\HomeInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Router\State\RouterState;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Class Router
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Router implements RouterInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

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
     * @param DispatcherInterface $dispatcher
     * @param RouteRepositoryInterface $routeRepository
     * @param HomeInterface $home
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(
        DispatcherInterface $dispatcher,
        RouteRepositoryInterface $routeRepository,
        HomeInterface $home,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository,
        BagFactoryInterface $bagFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->dispatcher = $dispatcher;
        $this->home = $home;
        $this->routeRepository = $routeRepository;
        $this->signalDefinitionRepository = $signalDefinitionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(EvaluationContextInterface $evaluationContext)
    {
        $homeRouteArgumentStaticBag = $this->home->attributeExpressionBagToStaticBag($evaluationContext);

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
        // and a corresponding new view state, so we can create a new app state
        $newAppState = $programState->withPage($newRouterState, $newPageViewState);

        $navigatedSignalDefinition = $this->signalDefinitionRepository->getByName(
            LibraryInterface::CORE,
            'navigated'
        );

        return $this->dispatcher->dispatchSignal(
            $program,
            $newAppState,
            $navigatedSignalDefinition,
            $this->bagFactory->createStaticBag([
                'library' => $libraryName,
                'route' => $routeName
            ])
        );
    }
}
