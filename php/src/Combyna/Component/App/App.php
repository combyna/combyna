<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\App\State\AppState;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventDispatcherInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramState;
use Combyna\Component\Router\RouterInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextTreeFactoryInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Class App
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class App implements AppInterface
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
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var OverlayViewCollectionInterface
     */
    private $overlayViewCollection;

    /**
     * @var PageViewCollectionInterface
     */
    private $pageViewCollection;

    /**
     * @var ProgramInterface
     */
    private $program;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SignalDefinitionRepositoryInterface
     */
    private $signalDefinitionRepository;

    /**
     * @var UiEvaluationContextTreeFactoryInterface
     */
    private $uiEvaluationContextTreeFactory;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param ExpressionFactoryInterface $expressionFactory
     * @param DispatcherInterface $dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface $router
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     * @param PageViewCollectionInterface $pageViewCollection
     * @param OverlayViewCollectionInterface $overlayViewCollection
     * @param UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
     * @param EnvironmentInterface $environment
     * @param ProgramInterface $program
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        ExpressionFactoryInterface $expressionFactory,
        DispatcherInterface $dispatcher,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory,
        EnvironmentInterface $environment,
        ProgramInterface $program
    ) {
        $this->bagFactory = $bagFactory;
        $this->dispatcher = $dispatcher;
        $this->environment = $environment;
        $this->eventDispatcher = $eventDispatcher;
        $this->expressionFactory = $expressionFactory;
        $this->overlayViewCollection = $overlayViewCollection;
        $this->pageViewCollection = $pageViewCollection;
        $this->program = $program;
        $this->router = $router;
        $this->signalDefinitionRepository = $signalDefinitionRepository;
        $this->uiEvaluationContextTreeFactory = $uiEvaluationContextTreeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState()
    {
        $routerState = $this->router->createInitialState($this->program->getRootEvaluationContext());

        return new AppState(
            // TODO: Factor this part out into Program::createInitialState()
            new ProgramState(
                $routerState,
                $this->pageViewCollection->createInitialState($routerState, $this->program->getRootEvaluationContext()),
                $this->overlayViewCollection->createInitialStates()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvent(
        AppStateInterface $appState,
        WidgetStatePathInterface $widgetStatePath,
        $libraryName,
        $eventName,
        array $payloadNatives = []
    ) {
        $widgetEvaluationContext = $this->uiEvaluationContextTreeFactory->createWidgetEvaluationContextTree(
            $widgetStatePath,
            $this->program,
            $this->environment
        );
        $widget = $widgetEvaluationContext->getWidget();
        $event = $widget->createEvent(
            $libraryName,
            $eventName,
            $this->bagFactory->coerceStaticBag($payloadNatives)
        );

        $newProgramState = $this->eventDispatcher->dispatchEvent(
            $appState->getProgramState(),
            $this->program,
            $event,
            $widget,
            $widgetEvaluationContext
        );

        return $appState->withProgramState($newProgramState);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchSignal(AppStateInterface $appState, $libraryName, $signalName, array $payloadNatives)
    {
        $signalDefinition = $this->signalDefinitionRepository->getByName($libraryName, $signalName);
        $payloadStaticBag = $this->bagFactory->coerceStaticBag($payloadNatives);

        $newProgramState = $this->dispatcher->dispatchSignal(
            $this->program,
            $appState->getProgramState(),
            $signalDefinition,
            $payloadStaticBag
        );

        return $appState->withProgramState($newProgramState);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageViewByName($name)
    {
        return $this->program->getPageViewByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEvaluationContext()
    {
        return $this->program->getRootEvaluationContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetByPath(array $names)
    {
        return $this->program->getWidgetByPath($names);
    }

    /**
     * {@inheritdoc}
     */
    public function navigateTo(AppStateInterface $appState, $libraryName, $routeName, array $routeArguments = [])
    {
        $newProgramState = $this->router->navigateTo(
            $appState->getProgramState(),
            $this->program,
            $libraryName,
            $routeName,
            $this->bagFactory->coerceStaticBag($routeArguments),
            $this->pageViewCollection,
            $this->program->getRootEvaluationContext()
        );

        return $appState->withProgramState($newProgramState);
    }
}
