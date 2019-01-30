<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class NonZeroNumberAssuranceNode
 *
 * Ensures that the given expression doesn't evaluate to zero
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssuranceNode extends AbstractActNode implements AssuranceNodeInterface
{
    const TYPE = 'non-zero-number-assurance';

    /**
     * @var ExpressionNodeInterface
     */
    private $inputExpressionNode;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param ExpressionNodeInterface $inputExpressionNode
     * @param string $name Name to expose the assured static to sub-expressions as
     */
    public function __construct(ExpressionNodeInterface $inputExpressionNode, $name)
    {
        $this->inputExpressionNode = $inputExpressionNode;
        $this->staticName = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->inputExpressionNode);

        // Check at compile-time that the expression can only resolve to a number
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->inputExpressionNode,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'non-zero assurance'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticName()
    {
        return $this->staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticTypeDeterminer()
    {
        // The only possible type this assured static can evaluate to is the result type of its expression
        return $this->inputExpressionNode->getResultTypeDeterminer();
    }

    /**
     * Fetches the input expression to be assured
     *
     * @return ExpressionNodeInterface
     */
    public function getInputExpression()
    {
        return $this->inputExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return NonZeroNumberAssurance::TYPE;
    }
}
