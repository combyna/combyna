<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class ViewEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewEvaluationContext extends AbstractEvaluationContext implements ViewEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactory
     */
    protected $evaluationContextFactory;

    /**
     * @var UiEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var StaticBagInterface|null
     */
    private $variableStaticBag;

    /**
     * @param UiEvaluationContextFactory $evaluationContextFactory
     * @param UiEvaluationContextInterface $parentContext
     * @param StaticBagInterface|null $variableStaticBag
     */
    public function __construct(
        UiEvaluationContextFactory $evaluationContextFactory,
        UiEvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->variableStaticBag = $variableStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createViewEvaluationContext($this, $variableStaticBag);
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
        return $this->evaluationContextFactory->createWidgetEvaluationContext($this, $widget);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        if ($this->variableStaticBag && $this->variableStaticBag->hasStatic($variableName)) {
            return $this->variableStaticBag->getStatic($variableName);
        }

        return $this->parentContext->getVariable($variableName);
    }
}
