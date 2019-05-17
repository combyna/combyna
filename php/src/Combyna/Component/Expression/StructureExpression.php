<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class StructureExpression
 *
 * Contains a list of expressions for attributes, which will be evaluated
 * in order to create a StaticStructureExpression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureExpression extends AbstractExpression
{
    const TYPE = 'structure';

    /**
     * @var ExpressionBagInterface
     */
    private $expressionBag;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param ExpressionBagInterface $expressionBag
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ExpressionBagInterface $expressionBag
    ) {
        $this->expressionBag = $expressionBag;
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $staticBag = $this->expressionBag->toStaticBag($subEvaluationContext);

        return $this->staticExpressionFactory->createStaticStructureExpression($staticBag);
    }
}
