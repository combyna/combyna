<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\MultipleType;
use Combyna\Type\StaticListType;
use Combyna\Type\StaticType;
use LogicException;

/**
 * Class ConcatenationExpression
 *
 * Concatenates a series of text or number values to form a string
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpression extends AbstractExpression
{
    const TYPE = 'concatenation';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionInterface
     */
    private $operandListExpression;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $operandListExpression
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $operandListExpression
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->operandListExpression = $operandListExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubContext($this);

        $operandListStatic = $this->operandListExpression->toStatic($subEvaluationContext);

        if (!$operandListStatic instanceof StaticListExpression) {
            throw new LogicException(
                'ConcatenationExpression :: List can only evaluate to a static-list ' .
                'or error expression, but got a(n) "' . $operandListStatic->getType() . '"'
            );
        }

        // NumberExpressions' floats or integers should be coerced to string at this point
        return $operandListStatic->concatenate();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(TextExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubContext($this);

        $this->operandListExpression->validate($subValidationContext);

        // Ensure the list operand can only ever evaluate to a list
        // with elements that evaluate only to either text or number statics
        $subValidationContext->assertResultType(
            $this->operandListExpression,
            new StaticListType(
                new MultipleType(
                    [
                        new StaticType(TextExpression::class),
                        new StaticType(NumberExpression::class)
                    ]
                )
            ),
            'operand list'
        );
    }
}
