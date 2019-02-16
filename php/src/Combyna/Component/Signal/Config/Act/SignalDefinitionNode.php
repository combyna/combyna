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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class SignalDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionNode extends AbstractActNode implements SignalDefinitionNodeInterface
{
    const TYPE = 'signal-definition';

    /**
     * @var bool
     */
    private $isBroadcast;

    /**
     * @var FixedStaticBagModelNode
     */
    private $payloadStaticBagModelNode;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $signalName
     * @param FixedStaticBagModelNode $payloadStaticBagModelNode
     * @param bool $isBroadcast
     */
    public function __construct($signalName, FixedStaticBagModelNode $payloadStaticBagModelNode, $isBroadcast = false)
    {
        $this->isBroadcast = $isBroadcast;
        $this->payloadStaticBagModelNode = $payloadStaticBagModelNode;
        $this->signalName = $signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->payloadStaticBagModelNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel(QueryRequirementInterface $queryRequirement)
    {
        return $this->payloadStaticBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticType($staticName, QueryRequirementInterface $queryRequirement)
    {
        $definition = $this->payloadStaticBagModelNode->getStaticDefinitionByName($staticName, $queryRequirement);

        return $queryRequirement->determineType($definition->getStaticTypeDeterminer());
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
        return $this->isBroadcast;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return true;
    }
}
