<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\ExpressionListInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class ListExpression
 *
 * Contains a list of expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListExpression extends AbstractExpression
{
    const TYPE = 'list';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionListInterface
     */
    private $expressionList;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param ExpressionListInterface $expressionList
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ExpressionListInterface $expressionList
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->expressionList = $expressionList;
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $staticList = $this->expressionList->toStaticList($subEvaluationContext);

        return $this->staticExpressionFactory->createStaticListExpression($staticList);
    }
}
