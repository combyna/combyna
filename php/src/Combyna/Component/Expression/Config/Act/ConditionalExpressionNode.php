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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ConditionalExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Type\AdditiveDeterminer;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->conditionExpression);
        $specBuilder->addChildNode($this->consequentExpression);
        $specBuilder->addChildNode($this->alternateExpression);

        // Ensure the condition expression can only ever evaluate to a boolean
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->conditionExpression,
                new PresolvedTypeDeterminer(new StaticType(BooleanExpression::class)),
                'condition'
            )
        );

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
    public function getResultTypeDeterminer()
    {
        return new AdditiveDeterminer([
            $this->consequentExpression->getResultTypeDeterminer(),
            $this->alternateExpression->getResultTypeDeterminer()
        ]);
    }
}
