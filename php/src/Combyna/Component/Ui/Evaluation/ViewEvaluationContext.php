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
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
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
     * @var ViewEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var StaticBagInterface|null
     */
    private $variableStaticBag;

    /**
     * @param UiEvaluationContextFactory $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param StaticBagInterface|null $variableStaticBag
     */
    public function __construct(
        UiEvaluationContextFactory $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->variableStaticBag = $variableStaticBag;
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
    public function getPath()
    {
        // RootViewEvaluationContext will already have provided the view's name
        return $this->parentContext->getPath();
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
