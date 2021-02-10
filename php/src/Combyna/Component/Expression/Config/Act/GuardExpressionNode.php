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
use Combyna\Component\Expression\Behaviour\Query\Specifier\AssuredStaticTypeQuerySpecifier;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\GuardExpression;
use Combyna\Component\Expression\Validation\Context\Specifier\AssuredContextSpecifier;
use Combyna\Component\Validator\Constraint\DescendantHasEquivalentQueryConstraint;
use Combyna\Component\Validator\Type\AdditiveDeterminer;

/**
 * Class GuardExpressionNode
 *
 * Evaluates a set of assurances and checks they meet a series of constraints.
 * If all results meet the assurance constraints, the consequent expression is returned,
 * otherwise the alternate one is
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GuardExpressionNode extends AbstractExpressionNode
{
    const TYPE = GuardExpression::TYPE;

    /**
     * @var ExpressionNodeInterface
     */
    private $alternateExpression;

    /**
     * @var AssuranceNodeInterface[]
     */
    private $assuranceNodes;

    /**
     * @var ExpressionNodeInterface
     */
    private $consequentExpression;

    /**
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @param ExpressionNodeInterface $consequentExpression
     * @param ExpressionNodeInterface $alternateExpression
     */
    public function __construct(
        array $assuranceNodes,
        ExpressionNodeInterface $consequentExpression,
        ExpressionNodeInterface $alternateExpression
    ) {
        $this->alternateExpression = $alternateExpression;
        $this->assuranceNodes = $assuranceNodes;
        $this->consequentExpression = $consequentExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->assuranceNodes as $assuranceNode) {
            $specBuilder->addChildNode($assuranceNode);

            $specBuilder->addConstraint(
                new DescendantHasEquivalentQueryConstraint(
                    new AssuredStaticTypeQuerySpecifier(
                        $assuranceNode->getAssuredStaticName()
                    )
                )
            );
        }

        $specBuilder->addSubSpec(function (BehaviourSpecBuilderInterface $subSpecBuilder) {
            // Only give the consequent and alternate expressions the assured context,
            // as the assurances cannot refer to each other's assured statics
            $subSpecBuilder->defineValidationContext(
                new AssuredContextSpecifier()
            );

            $subSpecBuilder->addChildNode($this->consequentExpression);
            $subSpecBuilder->addChildNode($this->alternateExpression);
        });
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
     * Fetches the assurances for this guard expression
     *
     * @return AssuranceNodeInterface[]
     */
    public function getAssuranceNodes()
    {
        return $this->assuranceNodes;
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
        // The result of the guard expression could be either the consequent or the alternate expression,
        // so return a type that specifies both
        return new AdditiveDeterminer([
            $this->consequentExpression->getResultTypeDeterminer(),
            $this->alternateExpression->getResultTypeDeterminer()
        ]);
    }
}
