<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoter;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;
use LogicException;

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
    public function definesStatic($staticName)
    {
        return $this->staticName === $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return AssuranceInterface::NON_ZERO_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredAssuredStaticNames()
    {
        return [$this->staticName];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionNodePromoter $expressionNodePromoter
    ) {
        return $expressionFactory->createGuardAssurance(
            $expressionNodePromoter->promote($this->inputExpressionNode),
            $this->getConstraint(),
            $this->staticName
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticType(ValidationContextInterface $validationContext, $assuredStaticName)
    {
        if ($assuredStaticName !== $this->staticName) {
            throw new LogicException(
                'NonZeroNumberAssurance only defines static "' . $this->staticName .
                '" but was asked about "' . $assuredStaticName . '"'
            );
        }

        // The only possible type this assured static can evaluate to is the result type of its expression
        return $this->inputExpressionNode->getResultType($validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $this->inputExpressionNode->validate($validationContext);

        // Check at compile-time that the expression can only resolve to a number
        $validationContext->assertResultType(
            $this->inputExpressionNode,
            new StaticType(NumberExpression::class),
            'non-zero assurance'
        );
    }
}
