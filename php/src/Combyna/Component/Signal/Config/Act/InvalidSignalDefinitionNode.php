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
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class InvalidSignalDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InvalidSignalDefinitionNode extends AbstractActNode implements SignalDefinitionNodeInterface
{
    const TYPE = 'invalid-signal-definition';

    /**
     * @var string
     */
    private $contextDescription;

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
     * @param string $contextDescription
     */
    public function __construct($libraryName, $signalName, $contextDescription)
    {
        $this->contextDescription = $contextDescription;
        $this->libraryName = $libraryName;
        $this->signalName = $signalName;
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
                    'Invalid signal "%s" of library "%s" - %s',
                    $this->signalName,
                    $this->libraryName,
                    $this->contextDescription
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
                'Payload static bag model for invalid signal "%s" of library "%s"',
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
                'Payload static "%s" for invalid signal "%s" of defined library "%s"',
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
        return false; // Invalid signals cannot be dispatched, let alone broadcast
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return false; // Invalid signal definition
    }
}
