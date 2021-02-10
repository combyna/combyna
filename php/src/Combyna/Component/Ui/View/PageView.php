<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBag;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextTreeFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\Store\ViewStoreInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use LogicException;

/**
 * Class PageView
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageView implements PageViewInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $name;

    /**
     * @var WidgetInterface
     */
    private $rootWidget;

    /**
     * @var ViewStoreInterface
     */
    private $store;

    /**
     * @var ExpressionInterface
     */
    private $titleExpression;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var UiEvaluationContextTreeFactoryInterface
     */
    private $uiEvaluationContextTreeFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @param string $name
     * @param ExpressionInterface $titleExpression
     * @param string $description
     * @param WidgetInterface $rootWidget
     * @param ViewStoreInterface $store
     * @param BagFactoryInterface $bagFactory
     * @param UiStateFactoryInterface $uiStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
     */
    public function __construct(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        WidgetInterface $rootWidget,
        ViewStoreInterface $store,
        BagFactoryInterface $bagFactory,
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->description = $description;
        $this->name = $name;
        $this->rootWidget = $rootWidget;
        $this->store = $store;
        $this->titleExpression = $titleExpression;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiEvaluationContextTreeFactory = $uiEvaluationContextTreeFactory;
        $this->uiStateFactory = $uiStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContext(
        EvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        PageViewStateInterface $pageViewState = null
    ) {
        return $this->uiEvaluationContextFactory->createRootViewEvaluationContext(
            $this,
            $parentContext,
            $parentContext->getEnvironment(),
            $pageViewState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(EvaluationContextInterface $rootEvaluationContext)
    {
        $storeState = $this->store->createInitialState($rootEvaluationContext);
        $viewAttributeStaticBag = new StaticBag([]); // FIXME
        $rootViewEvaluationContext = $this->createEvaluationContext(
            $rootEvaluationContext,
            $this->uiEvaluationContextFactory
        );

        $rootWidgetState = $this->rootWidget->createInitialState(
            'root',
            $rootViewEvaluationContext,
            $this->uiEvaluationContextFactory
        );

        return $this->uiStateFactory->createPageViewState(
            $this,
            $storeState,
            $rootWidgetState,
            $viewAttributeStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetByPath(array $names)
    {
        $rootWidgetName = array_shift($names);

        if ($rootWidgetName !== 'root') {
            throw new LogicException('Root widget state for a view must be named "root"');
        }

        if (count($names) === 0) {
            // Fetching the root widget itself
            return $this->rootWidget;
        }

        return $this->rootWidget->getDescendantByPath($names);
    }

    /**
     * {@inheritdoc}
     */
    public function handleSignal(
        PageViewStateInterface $pageViewState,
        SignalInterface $signal,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $originalPageViewState = $pageViewState;

        $evaluationContext = $this->createEvaluationContext(
            $program->getRootEvaluationContext(),
            $this->uiEvaluationContextFactory,
            $pageViewState
        );

        $newStoreState = $this->store->handleSignal(
            $signal,
            $pageViewState->getStoreState(),
            $evaluationContext
        );

        $pageViewState = $pageViewState->withStoreState($newStoreState);

        // TODO: Now broadcast the signal to all widget stores' signal handlers

        if ($pageViewState !== $originalPageViewState) {
            // Store has updated - rerender all widgets in the view, but maintain
            // the state of any stores where the widget was visible before and remains visible now

            // Create a new evaluation context with the updated page view state
            $evaluationContext = $this->createEvaluationContext(
                $program->getRootEvaluationContext(),
                $this->uiEvaluationContextFactory,
                $pageViewState
            );

            $newRootWidgetState = $this->rootWidget->reevaluateState(
                $pageViewState->getRootWidgetState(),
                $evaluationContext,
                $this->uiEvaluationContextFactory
            );

            // TODO: Just use a ->withRootWidgetState(...) method - we already have the new store state from above
            return $pageViewState->withState($newStoreState, $newRootWidgetState);
        }

        return $pageViewState;
    }

    /**
     * {@inheritdoc}
     */
    public function makeStoreQuery(
        $queryName,
        StaticBagInterface $argumentStaticBag,
        ViewEvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    ) {
        return $this->store->makeQuery(
            $queryName,
            $argumentStaticBag,
            $evaluationContext,
            $viewStoreState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        PageViewStateInterface $oldState,
        EvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        $evaluationContext = $this->createEvaluationContext(
            $evaluationContext,
            $this->uiEvaluationContextFactory,
            $oldState
        );

        $newRootWidgetState = $this->rootWidget->reevaluateState(
            $oldState->getRootWidgetState(),
            $evaluationContext,
            $this->uiEvaluationContextFactory
        );

        return $oldState->withRootWidgetState($newRootWidgetState);
    }
}
