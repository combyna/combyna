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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;

/**
 * Class EventDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNode extends AbstractActNode implements EventDefinitionNodeInterface
{
    const TYPE = 'event-definition';

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $payloadStaticBagModelNode;

    /**
     * @param string $eventName
     * @param FixedStaticBagModelNodeInterface $payloadStaticBagModelNode
     */
    public function __construct($eventName, FixedStaticBagModelNodeInterface $payloadStaticBagModelNode)
    {
        $this->eventName = $eventName;
        $this->payloadStaticBagModelNode = $payloadStaticBagModelNode;
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
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->eventName;
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
    public function getPayloadStaticType($payloadStaticName)
    {
        return $this->payloadStaticBagModelNode->getStaticType($payloadStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return true;
    }
}
