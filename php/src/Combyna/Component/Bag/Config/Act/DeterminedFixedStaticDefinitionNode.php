<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\Validation\Constraint\ResolvableTypeConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class DeterminedFixedStaticDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DeterminedFixedStaticDefinitionNode extends AbstractActNode implements DeterminedFixedStaticDefinitionInterface, FixedStaticDefinitionNodeInterface
{
    const TYPE = 'determined-fixed-static-definition';

    /**
     * @var ExpressionNodeInterface|null
     */
    private $defaultExpressionNode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var TypeInterface
     */
    private $staticType;

    /**
     * @param string $name
     * @param TypeInterface $staticType
     * @param ExpressionNodeInterface|null $defaultExpressionNode
     */
    public function __construct(
        $name,
        TypeInterface $staticType,
        ExpressionNodeInterface $defaultExpressionNode = null
    ) {
        $this->defaultExpressionNode = $defaultExpressionNode;
        $this->name = $name;
        $this->staticType = $staticType;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticDefinition(DeterminedFixedStaticDefinitionInterface $otherDefinition)
    {
        return $this->staticType->allows($otherDefinition->getStaticType());
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        if ($this->defaultExpressionNode) {
            $specBuilder->addChildNode($this->defaultExpressionNode);

            // Make sure the default expression is allowed by the type of the definition
            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->defaultExpressionNode,
                    new PresolvedTypeDeterminer($this->staticType),
                    'default expression'
                )
            );
        }

        // Make sure the static's type is a resolvable, valid type
        $specBuilder->addConstraint(new ResolvableTypeConstraint(new PresolvedTypeDeterminer($this->staticType)));
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultExpression()
    {
        return $this->defaultExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedStaticType()
    {
        return $this->staticType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticType()
    {
        return $this->staticType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeSummary()
    {
        return $this->staticType->getSummary();
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeSummaryWithValue()
    {
        return $this->staticType->getSummaryWithValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeDeterminer()
    {
        return new PresolvedTypeDeterminer($this->staticType);
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->defaultExpressionNode === null;
    }

    /**
     * {@inheritdoc}
     */
    public function staticTypeHasValue()
    {
        return $this->staticType->hasValue();
    }

    /**
     * {@inheritdoc}
     */
    public function validateExpression(
        ExpressionNodeInterface $expressionNode,
        ValidationContextInterface $validationContext,
        $contextDescription
    ) {
        $expressionResultType = $validationContext->getExpressionResultType($expressionNode);

        if (!$this->staticType->allows($expressionResultType)) {
            $validationContext->addTypeMismatchViolation(
                $this->staticType,
                $expressionResultType,
                $contextDescription . ' ' . $this->name
            );
        }
    }
}
