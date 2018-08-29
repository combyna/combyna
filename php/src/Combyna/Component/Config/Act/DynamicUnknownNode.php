<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class DynamicUnknownNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DynamicUnknownNode extends AbstractActNode implements DynamicActNodeInterface
{
    const TYPE = 'unknown';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($contextDescription, QueryRequirementInterface $queryRequirement)
    {
        $this->contextDescription = $contextDescription;

        // Apply the validation for this dynamically created ACT node
        $queryRequirement->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                '[Unknown node] ' .
                $this->contextDescription
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
