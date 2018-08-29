<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Expression\Config\Act\DynamicUnknownExpressionNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class DynamicUnknownQueryNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DynamicUnknownQueryNode extends AbstractActNode implements DynamicActNodeInterface, QueryNodeInterface
{
    const TYPE = 'unknown-store-query';

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
    public function __construct(
        $name,
        QueryRequirementInterface $queryRequirement
    ) {
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
        $specBuilder->addConstraint(new KnownFailureConstraint(sprintf('Unknown query node "%s"', $this->name)));
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression()
    {
        return new DynamicUnknownExpressionNode(
            sprintf('Unknown query node "%s" expression', $this->name),
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
    public function getParameterBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf('Unknown query node "%s" parameter bag model', $this->name),
            $this->queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        // Nothing to do, validation should already have been marked failed by the above
    }
}
