<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Bag\Config\Act\DynamicUnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class DynamicUnknownSignalDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DynamicUnknownSignalDefinitionNode extends AbstractActNode implements SignalDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-signal-definition';

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $libraryName
     * @param string $signalName
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($libraryName, $signalName, QueryRequirementInterface $queryRequirement)
    {
        $this->libraryName = $libraryName;
        $this->signalName = $signalName;

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
                    'Library "%s" does not define signal "%s"',
                    $this->libraryName,
                    $this->signalName
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel(QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownFixedStaticBagModelNode(
            sprintf(
                'Payload static bag for undefined signal "%s" of defined library "%s"',
                $this->signalName,
                $this->libraryName
            ),
            $queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticType($staticName, QueryRequirementInterface $queryRequirement)
    {
        return new UnresolvedType(
            sprintf(
                'Payload static "%s" for undefined signal "%s" of defined library "%s"',
                $staticName,
                $this->signalName,
                $this->libraryName
            )
        );
    }

    /**
     * Fetches the unique name of the signal
     *
     * @return string
     */
    public function getSignalName()
    {
        return $this->signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function isBroadcast()
    {
        return false; // Unknown signals cannot be dispatched, let alone broadcast
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return false; // Unknown signal definition
    }
}
