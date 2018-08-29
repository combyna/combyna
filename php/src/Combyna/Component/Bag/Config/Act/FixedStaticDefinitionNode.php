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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\Validation\Constraint\ResolvedTypeConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class FixedStaticDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticDefinitionNode extends AbstractActNode implements FixedStaticDefinitionNodeInterface
{
    const TYPE = 'fixed-static-definition';

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
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        if ($this->defaultExpressionNode) {
            $specBuilder->addChildNode($this->defaultExpressionNode);
        }

        // Make sure the static's type is a resolved, valid type
        $specBuilder->addConstraint(new ResolvedTypeConstraint($this->staticType));
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
    public function getStaticType()
    {
        return $this->staticType;
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
