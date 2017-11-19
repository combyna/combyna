<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ConditionalExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class ConditionalExpressionNode
 *
 * Returns one expression if the condition evaluates to true and the other if false
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalExpressionNode extends AbstractExpressionNode
{
    const TYPE = ConditionalExpression::TYPE;

    /**
     * @var ExpressionNodeInterface
     */
    private $alternateExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $conditionExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $consequentExpression;

    /**
     * @param ExpressionNodeInterface $conditionExpression
     * @param ExpressionNodeInterface $consequentExpression
     * @param ExpressionNodeInterface $alternateExpression
     */
    public function __construct(
        ExpressionNodeInterface $conditionExpression,
        ExpressionNodeInterface $consequentExpression,
        ExpressionNodeInterface $alternateExpression
    ) {
        $this->alternateExpression = $alternateExpression;
        $this->conditionExpression = $conditionExpression;
        $this->consequentExpression = $consequentExpression;
    }

    /**
     * Fetches the alternate expression
     *
     * @return ExpressionNodeInterface
     */
    public function getAlternateExpression()
    {
        return $this->alternateExpression;
    }

    /**
     * Fetches the condition expression
     *
     * @return ExpressionNodeInterface
     */
    public function getConditionExpression()
    {
        return $this->conditionExpression;
    }

    /**
     * Fetches the consequent expression
     *
     * @return ExpressionNodeInterface
     */
    public function getConsequentExpression()
    {
        return $this->consequentExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $consequentResultType = $this->consequentExpression->getResultType($subValidationContext);
        $alternateResultType = $this->alternateExpression->getResultType($subValidationContext);

        return $consequentResultType->mergeWith($alternateResultType);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->conditionExpression->validate($subValidationContext);
        $this->consequentExpression->validate($subValidationContext);
        $this->alternateExpression->validate($subValidationContext);

        // Ensure the condition expression can only ever evaluate to a boolean
        $subValidationContext->assertResultType(
            $this->conditionExpression,
            new StaticType(BooleanExpression::class),
            'condition'
        );
    }
}
