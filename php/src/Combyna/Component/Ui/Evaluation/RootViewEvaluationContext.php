<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\View\ViewInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use LogicException;

/**
 * Class RootViewEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootViewEvaluationContext extends AbstractEvaluationContext implements ViewEvaluationContextInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var RouterStateInterface
     */
    private $routerState;

    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @var PageViewStateInterface|null
     */
    private $viewState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewInterface $view
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     * @param RouterStateInterface $routerState
     * @param PageViewStateInterface|null $viewState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewInterface $view,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment,
        RouterStateInterface $routerState,
        PageViewStateInterface $viewState = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->environment = $environment;
        $this->routerState = $routerState;
        $this->view = $view;
        $this->viewState = $viewState;
    }

    /**
     * {@inheritdoc}
     */
    public function bubbleEventToParent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetInterface $initialWidget
    ) {
        // There are no more widgets in the tree to bubble to, so there's nothing to do
        return $programState;
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createViewEvaluationContext(
            $this,
            $variableStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createSubStoreContext(UiStoreStateInterface $storeState)
    {
        return $this->evaluationContextFactory->createViewStoreEvaluationContext($this, $storeState);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompoundWidgetDefinitionContext()
    {
        throw new LogicException('Cannot fetch compound widget definition context outside a compound widget');
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return [$this->view->getName()];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteArgument($parameterName)
    {
        return $this->routerState
            ->getRouteArgumentBag()
            ->getStatic($parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function makeViewStoreQuery($queryName, StaticBagInterface $argumentStaticBag)
    {
        $viewStoreState = $this->viewState ?
            // A previous state already exists - make the query in the context of the existing state
            $this->viewState->getStoreState() :
            // No state exists yet - create a new one to make the query in the context of
            $this->view->getStore()->createInitialState($this);

        return $this->view->makeStoreQuery($queryName, $argumentStaticBag, $this, $viewStoreState);
    }
}
