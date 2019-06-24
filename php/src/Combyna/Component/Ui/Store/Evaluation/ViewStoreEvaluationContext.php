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

use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
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
     * @var ViewEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var UiStoreStateInterface
     */
    private $viewStoreState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param UiStoreStateInterface $viewStoreState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        UiStoreStateInterface $viewStoreState
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->viewStoreState = $viewStoreState;
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
    public function getCompoundWidgetDefinitionContext()
    {
        // TODO: Restructure interfaces so that this method stub is not needed here
        throw new LogicException('View stores cannot access compound widget definitions');
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return array_merge($this->parentContext->getPath(), ['store']);
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
}
