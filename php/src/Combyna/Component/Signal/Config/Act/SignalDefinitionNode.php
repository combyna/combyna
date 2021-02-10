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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicContainerNode;

/**
 * Class SignalDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionNode extends AbstractActNode implements SignalDefinitionNodeInterface
{
    const TYPE = 'signal-definition';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var bool
     */
    private $isBroadcast;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $payloadStaticBagModelNode;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $libraryName
     * @param string $signalName
     * @param FixedStaticBagModelNodeInterface $payloadStaticBagModelNode
     * @param bool $isBroadcast
     */
    public function __construct(
        $libraryName,
        $signalName,
        FixedStaticBagModelNodeInterface $payloadStaticBagModelNode,
        $isBroadcast = false
    ) {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->isBroadcast = $isBroadcast;
        $this->libraryName = $libraryName;
        $this->payloadStaticBagModelNode = $payloadStaticBagModelNode;
        $this->signalName = $signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);
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
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel()
    {
        return $this->payloadStaticBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticType($staticName)
    {
        $definition = $this->payloadStaticBagModelNode->getStaticDefinitionByName(
            $staticName,
            $this->dynamicContainerNode
        );

        return $this->dynamicContainerNode->determineType($definition->getStaticTypeDeterminer());
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
