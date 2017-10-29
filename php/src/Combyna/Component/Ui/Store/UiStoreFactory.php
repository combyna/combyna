<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Signal\SignalDefinitionReferenceInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Store\Instruction\ViewStoreInstructionListInterface;
use Combyna\Component\Ui\Store\Query\StoreQuery;
use Combyna\Component\Ui\Store\Query\StoreQueryCollection;
use Combyna\Component\Ui\Store\Query\StoreQueryCollectionInterface;
use Combyna\Component\Ui\Store\Signal\ViewStoreSignalHandler;

/**
 * Class UiStoreFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiStoreFactory implements UiStoreFactoryInterface
{
    /**
     * @var UiStateFactoryInterface
     */
    private $stateFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @param UiStateFactoryInterface $stateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     */
    public function __construct(
        UiStateFactoryInterface $stateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
    ) {
        $this->stateFactory = $stateFactory;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNullViewStore($viewName)
    {
        return new NullViewStore($this->stateFactory, $viewName);
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery(
        $name,
        FixedStaticBagModelInterface $parameterStaticBagModel,
        ExpressionInterface $expression
    ) {
        return new StoreQuery($name, $parameterStaticBagModel, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryCollection(array $queries)
    {
        return new StoreQueryCollection($queries);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStore(
        $viewName,
        FixedStaticBagModelInterface $slotStaticBagModel,
        array $signalHandlers,
        array $commands,
        StoreQueryCollectionInterface $queryCollection
    ) {
        return new ViewStore(
            $this->stateFactory,
            $this->uiEvaluationContextFactory,
            $viewName,
            $slotStaticBagModel,
            $signalHandlers,
            $commands,
            $queryCollection
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreSignalHandler(
        SignalDefinitionReferenceInterface $signalDefinitionReference,
        ViewStoreInstructionListInterface $instructionList,
        ExpressionInterface $guardExpression = null
    ) {
        return new ViewStoreSignalHandler($signalDefinitionReference, $instructionList, $guardExpression);
    }
}
