<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Act;

use Combyna\Component\Bag\Config\Act\DynamicUnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class UnknownEventDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownEventDefinitionNode extends AbstractActNode implements EventDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-event-definition';

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var QueryRequirementInterface
     */
    private $queryRequirement;

    /**
     * @param string $libraryName
     * @param string $eventName
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($libraryName, $eventName, QueryRequirementInterface $queryRequirement)
    {
        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
        $this->queryRequirement = $queryRequirement;

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
                sprintf(
                    'Library "%s" does not define event "%s"',
                    $this->libraryName,
                    $this->eventName
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel()
    {
        return new DynamicUnknownFixedStaticBagModelNode(
            sprintf(
                'Payload static bag for undefined event "%s" of defined library "%s"',
                $this->eventName,
                $this->libraryName
            ),
            $this->queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticType($staticName)
    {
        return new UnresolvedType(
            sprintf(
                'Payload static "%s" for undefined event "%s" of defined library "%s"',
                $staticName,
                $this->eventName,
                $this->libraryName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return false; // Unknown event definition
    }
}
