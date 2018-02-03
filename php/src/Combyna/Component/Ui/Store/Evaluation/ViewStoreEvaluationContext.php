<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Evaluation;

use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use LogicException;

/**
 * Class ViewStoreEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreEvaluationContext extends AbstractEvaluationContext implements ViewStoreEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var UiStoreStateInterface
     */
    private $viewStoreState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param UiStoreStateInterface $viewStoreState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        UiStoreStateInterface $viewStoreState
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->viewStoreState = $viewStoreState;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidget($childName)
    {
        // TODO: Restructure interfaces so that this method stub is not needed here
        throw new LogicException('View stores cannot define child widgets');
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreSlotStatic($name)
    {
        return $this->viewStoreState->getSlotStatic($name);
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
}
