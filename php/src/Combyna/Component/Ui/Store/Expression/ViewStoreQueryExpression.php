<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\Expression\UiExpressionFactoryInterface;

/**
 * Class ViewStoreQueryExpression
 *
 * Makes a query on a store inside the current view.
 * View stores are only accessible from inside themselves
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreQueryExpression extends AbstractExpression
{
    const TYPE = 'view-store-query';

    /**
     * @var ExpressionBagInterface
     */
    private $argumentExpressionBag;

    /**
     * @var UiExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $queryName;

    /**
     * @param UiExpressionFactoryInterface $expressionFactory
     * @param string $queryName
     * @param ExpressionBagInterface $argumentExpressionBag
     */
    public function __construct(
        UiExpressionFactoryInterface $expressionFactory,
        $queryName,
        ExpressionBagInterface $argumentExpressionBag
    ) {
        $this->argumentExpressionBag = $argumentExpressionBag;
        $this->expressionFactory = $expressionFactory;
        $this->queryName = $queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $argumentStaticBag = $this->argumentExpressionBag->toStaticBag($evaluationContext);

        return $evaluationContext->makeViewStoreQuery($this->queryName, $argumentStaticBag);
    }
}
