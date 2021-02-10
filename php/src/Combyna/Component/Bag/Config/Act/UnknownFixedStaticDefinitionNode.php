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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use LogicException;

/**
 * Class UnknownFixedStaticDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFixedStaticDefinitionNode extends AbstractActNode implements DeterminedFixedStaticDefinitionInterface, FixedStaticDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-fixed-static-definition';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($name, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->name = $name;

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticDefinition(DeterminedFixedStaticDefinitionInterface $otherDefinition)
    {
        return false; // Unknown definitions cannot allow any others
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);

        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                'Unknown fixed static "' . $this->name . '"'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        throw new LogicException('Cannot determine an unknown fixed static definition node');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultExpression()
    {
        return new UnknownExpressionNode(
            'Unknown fixed static "' . $this->name . '"',
            $this->dynamicContainerNode
        );
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
        return $this->getStaticType();
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticType()
    {
        return new UnresolvedType('Unknown fixed static "' . $this->name . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeSummary()
    {
        return $this->getStaticType()->getSummary();
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeDeterminer()
    {
        return new PresolvedTypeDeterminer($this->getStaticType());
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validateExpression(
        ExpressionNodeInterface $expressionNode,
        ValidationContextInterface $validationContext,
        $contextDescription
    ) {
        // Unable to validate the expression as the static definition isn't known -
        // validation will fail for this node anyway, so no need to handle here
    }
}
