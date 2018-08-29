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
use Combyna\Component\Expression\Config\Act\DynamicUnknownExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class UnknownFixedStaticDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFixedStaticDefinitionNode extends AbstractActNode implements FixedStaticDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-fixed-static-definition';

    /**
     * @var string
     */
    private $name;

    /**
     * @var QueryRequirementInterface
     */
    private $queryRequirement;

    /**
     * @param string $name
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($name, QueryRequirementInterface $queryRequirement)
    {
        $this->name = $name;
        $this->queryRequirement = $queryRequirement;

        // Apply the validation for this dynamically created ACT node
        $queryRequirement->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                'Unknown fixed static "' . $this->name . '"'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultExpression()
    {
        return new DynamicUnknownExpressionNode(
            'Unknown fixed static "' . $this->name . '"',
            $this->queryRequirement
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
    public function getStaticType()
    {
        return new UnresolvedType('Unknown fixed static "' . $this->name . '"');
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
