<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Query;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
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
        UiEvaluationContextInterface $evaluationContext,
        UiStoreStateInterface $storeState
    ) {
        $this->parameterStaticBagModel->assertValidStaticBag($argumentStaticBag);

        $subEvaluationContext = $evaluationContext->createSubStoreContext($storeState);

        return $this->expression->toStatic($subEvaluationContext);
    }
}
