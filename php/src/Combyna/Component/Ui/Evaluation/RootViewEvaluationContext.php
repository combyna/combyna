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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
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
     * @var ViewInterface
     */
    private $view;

    /**
     * @var StaticBagInterface|null
     */
    private $viewAttributeStaticBag;

    /**
     * @var ViewStoreStateInterface
     */
    private $viewStoreState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewInterface $view
     * @param ViewStoreStateInterface $viewStoreState
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewInterface $view,
        ViewStoreStateInterface $viewStoreState,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->environment = $environment;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
        $this->view = $view;
        $this->viewAttributeStaticBag = $viewAttributeStaticBag;
        $this->viewStoreState = $viewStoreState;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction(
        $libraryName,
        $functionName,
        StaticBagInterface $argumentStaticBag,
        TypeInterface $returnType
    ) {
        return $this->parentContext->callFunction($libraryName, $functionName, $argumentStaticBag, $returnType);
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
    public function createSubWidgetEvaluationContext(WidgetInterface $widget)
    {
        return $widget->createEvaluationContext($this, $this->evaluationContextFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidget($childName)
    {
        throw new LogicException(sprintf(
            'Cannot fetch child "%s" from outside a compound widget',
            $childName
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function makeViewStoreQuery($queryName, StaticBagInterface $argumentStaticBag)
    {
        return $this->view->makeStoreQuery($queryName, $argumentStaticBag, $this, $this->viewStoreState);
    }
}
