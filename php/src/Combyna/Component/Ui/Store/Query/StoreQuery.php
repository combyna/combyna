<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Query;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;

/**
 * Class StoreQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StoreQuery implements StoreQueryInterface
{
    /**
     * @var ExpressionInterface
     */
    private $expression;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $parameterStaticBagModel;

    /**
     * @param string $name
     * @param FixedStaticBagModelInterface $parameterStaticBagModel
     * @param ExpressionInterface $expression
     */
    public function __construct(
        $name,
        FixedStaticBagModelInterface $parameterStaticBagModel,
        ExpressionInterface $expression
    ) {
        $this->expression = $expression;
        $this->name = $name;
        $this->parameterStaticBagModel = $parameterStaticBagModel;
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
    public function make(
        StaticBagInterface $argumentStaticBag,
        ViewEvaluationContextInterface $evaluationContext,
        UiStoreStateInterface $storeState
    ) {
        $argumentStaticBag = $this->parameterStaticBagModel->coerceStaticBag(
            $argumentStaticBag,
            $evaluationContext
        );

        // Provide the store's context (for access to slots etc.)
        $storeEvaluationContext = $evaluationContext->createSubStoreContext($storeState);

        // Provide the arguments passed for the parameters of the query
        $subEvaluationContext = $storeEvaluationContext->createSubScopeContext($argumentStaticBag);

        return $this->expression->toStatic($subEvaluationContext);
    }
}
