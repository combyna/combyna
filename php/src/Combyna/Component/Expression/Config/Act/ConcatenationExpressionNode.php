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
use Combyna\Component\Expression\ConcatenationExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class ConcatenationExpressionNode
 *
 * Concatenates a series of text or number values to form a string
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionNode extends AbstractExpressionNode
{
    const TYPE = ConcatenationExpression::TYPE;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $glueExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $operandListExpression;

    /**
     * @param ExpressionNodeInterface $operandListExpression
     * @param ExpressionNodeInterface|null $glueExpression
     */
    public function __construct(
        ExpressionNodeInterface $operandListExpression,
        ExpressionNodeInterface $glueExpression = null
    ) {
        $this->glueExpression = $glueExpression;
        $this->operandListExpression = $operandListExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->operandListExpression);

        // Ensure the list operand can only ever evaluate to a list
        // with elements that evaluate only to either text or number statics
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
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
            )
        );
    }

    /**
     * Fetches the glue expression, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getGlueExpression()
    {
        return $this->glueExpression;
    }

    /**
     * Fetches the operand list expression
     *
     * @return ExpressionNodeInterface
     */
    public function getOperandListExpression()
    {
        return $this->operandListExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new PresolvedTypeDeterminer(new StaticType(TextExpression::class));
    }
}
