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
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\View\ViewInterface;
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
     * @var ViewInterface
     */
    private $view;

    /**
     * @var ViewStateInterface|null
     */
    private $viewState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewInterface $view
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     * @param ViewStateInterface|null $viewState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewInterface $view,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment,
        ViewStateInterface $viewState = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->environment = $environment;
        $this->view = $view;
        $this->viewState = $viewState;
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
    public function getChildOfCurrentCompoundWidget($childName)
    {
        throw new LogicException(sprintf(
            'Cannot fetch child "%s" from outside a compound widget',
            $childName
        ));
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
