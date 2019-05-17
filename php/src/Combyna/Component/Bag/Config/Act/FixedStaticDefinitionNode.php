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
use Combyna\Component\Type\Config\Act\TypeNode;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\Validation\Constraint\ResolvableTypeConstraint;
use Combyna\Component\Validator\Constraint\CallbackConstraint;
use Combyna\Component\Validator\Context\NullValidationContext;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

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
     * @var TypeInterface|null
     */
    private $resolvedStaticType = null;

    /**
     * @var TypeDeterminerInterface
     */
    private $staticTypeDeterminer;

    /**
     * @param string $name
     * @param TypeDeterminerInterface $staticTypeDeterminer
     * @param ExpressionNodeInterface|null $defaultExpressionNode
     */
    public function __construct(
        $name,
        TypeDeterminerInterface $staticTypeDeterminer,
        ExpressionNodeInterface $defaultExpressionNode = null
    ) {
        $this->defaultExpressionNode = $defaultExpressionNode;
        $this->name = $name;
        $this->staticTypeDeterminer = $staticTypeDeterminer;
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
                    $this->staticTypeDeterminer,
                    'default expression'
                )
            );
        }

        // Make sure the static's type is a resolvable, valid type
        $specBuilder->addConstraint(new ResolvableTypeConstraint($this->staticTypeDeterminer));

        // Resolve the static's type once all static values are known,
        // as it can be different depending on the types of the other statics in the bag
        // (eg. if a custom TypeDeterminer is used for a static's type)
        $specBuilder->addConstraint(
            new CallbackConstraint(
                function (ValidationContextInterface $validationContext) {
                    $this->resolvedStaticType = $this->staticTypeDeterminer->determine($validationContext);
                }
            )
        );

        // Make sure the static's type itself is validated as necessary
        $specBuilder->addChildNode(new TypeNode($this->staticTypeDeterminer));
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return new DeterminedFixedStaticDefinitionNode(
            $this->name,
            $this->staticTypeDeterminer->determine($validationContext),
            $this->defaultExpressionNode
        );
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
        if ($this->resolvedStaticType !== null) {
            return $this->resolvedStaticType;
        }

        return $this->staticTypeDeterminer->determine(new NullValidationContext());
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeDeterminer()
    {
        return $this->staticTypeDeterminer;
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
        $staticType = $this->staticTypeDeterminer->determine($validationContext);

        if (!$staticType->allows($expressionResultType)) {
            $validationContext->addTypeMismatchViolation(
                $staticType,
                $expressionResultType,
                $contextDescription . ' ' . $this->name
            );
        }
    }
}
