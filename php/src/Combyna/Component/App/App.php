<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\App\Exception\EventDispatchFailedException;
use Combyna\Component\App\Exception\SignalDispatchFailedException;
use Combyna\Component\App\State\AppState;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Event\EventDispatcherInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Router\RouterInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextTreeFactoryInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
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
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

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
     * @param ExpressionFactoryInterface $expressionFactory
     * @param DispatcherInterface $dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface $router
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     * @param PageViewCollectionInterface $pageViewCollection
     * @param OverlayViewCollectionInterface $overlayViewCollection
     * @param UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
     * @param EnvironmentInterface $environment
     * @param ResourceRepositoryInterface $resourceRepository
     * @param ProgramInterface $program
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        DispatcherInterface $dispatcher,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory,
        EnvironmentInterface $environment,
        ResourceRepositoryInterface $resourceRepository,
        ProgramInterface $program
    ) {
        $this->dispatcher = $dispatcher;
        $this->environment = $environment;
        $this->eventDispatcher = $eventDispatcher;
        $this->expressionFactory = $expressionFactory;
        $this->overlayViewCollection = $overlayViewCollection;
        $this->pageViewCollection = $pageViewCollection;
        $this->program = $program;
        $this->resourceRepository = $resourceRepository;
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

        return new AppState($this->program->createInitialState($routerState));
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
        $widgetEndRenderableStatePath = $widgetStatePath->getEventualEndRenderableStatePath();
        $widgetEvaluationContext = $this->uiEvaluationContextTreeFactory->createWidgetEvaluationContextTree(
            $widgetEndRenderableStatePath,
            $this->program,
            $this->environment
        );
        $widget = $widgetEvaluationContext->getWidget();

        try {
            $event = $widget->createEvent(
                $libraryName,
                $eventName,
                $payloadNatives,
                $widgetEvaluationContext
            );
        } catch (EventDefinitionNotReferencedByWidgetException $exception) {
            throw new EventDispatchFailedException($exception->getMessage());
        } catch (IncompatibleNativeForCoercionException $exception) {
            throw new EventDispatchFailedException($exception->getMessage());
        } catch (LibraryNotInstalledException $exception) {
            throw new EventDispatchFailedException($exception->getMessage());
        }

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

        try {
            $payloadStaticBag = $signalDefinition
                ->getPayloadStaticBagModel()
                ->coerceNativeArrayToBag(
                    $payloadNatives,
                    $this->program->getRootEvaluationContext()
                );
        } catch (IncompatibleNativeForCoercionException $exception) {
            throw new SignalDispatchFailedException($exception->getMessage());
        }

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
    public function getProgram()
    {
        return $this->program;
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
        $route = $this->resourceRepository->getRouteByName($libraryName, $routeName);
        $routeArgumentStaticBag = $route->getParameterBagModel()
            ->coerceNativeArrayToBag(
                $routeArguments,
                $this->program->getRootEvaluationContext()
            );

        return $appState->withProgramState(
            $this->program->navigateTo(
                $appState->getProgramState(),
                $libraryName,
                $routeName,
                $routeArgumentStaticBag
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateUiState(AppStateInterface $appState)
    {
        return $appState->withProgramState(
            $this->program->reevaluateUiState(
                $appState->getProgramState()
            )
        );
    }
}
