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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class DynamicUnknownExpressionNode
 *
 * Represents a node in the ACT with an unknown type, making it invalid
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DynamicUnknownExpressionNode extends AbstractExpressionNode implements DynamicActNodeInterface
{
    const TYPE = 'unknown';

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($type, QueryRequirementInterface $queryRequirement)
    {
        $this->type = $type;

        // Apply the validation for this dynamically created ACT node
        $queryRequirement->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new KnownFailureConstraint('Node is of unknown type "' . $this->type . '"'));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new PresolvedTypeDeterminer(new UnresolvedType('Expression type "' . $this->type . '"'));
    }
}
