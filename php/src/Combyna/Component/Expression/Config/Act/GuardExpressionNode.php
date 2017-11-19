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

use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\GuardExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;

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
    public function getResultType(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $consequentResultType = $this->consequentExpression->getResultType($subValidationContext);
        $alternateResultType = $this->alternateExpression->getResultType($subValidationContext);

        // The result of the guard expression could be either the consequent or the alternate expression,
        // so return a type that specifies both
        return $consequentResultType->mergeWith($alternateResultType);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->assuranceNodes as $assuranceNode) {
            $assuranceNode->validate($subValidationContext);
        }

        $this->alternateExpression->validate($subValidationContext);

        $assuredValidationContext = $subValidationContext->createSubAssuredContext($this->assuranceNodes);

        $this->consequentExpression->validate($assuredValidationContext);

        $assuredValidationContext->assertAllRequiredAssuredStaticsWereUsed();
    }
}
