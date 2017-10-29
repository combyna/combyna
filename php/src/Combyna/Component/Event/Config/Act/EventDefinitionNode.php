<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class EventDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNode extends AbstractActNode
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var FixedStaticBagModelNode
     */
    private $payloadStaticBagModelNode;

    /**
     * @param string $eventName
     * @param FixedStaticBagModelNode $payloadStaticBagModelNode
     */
    public function __construct($eventName, FixedStaticBagModelNode $payloadStaticBagModelNode)
    {
        $this->eventName = $eventName;
        $this->payloadStaticBagModelNode = $payloadStaticBagModelNode;
    }

    /**
     * Fetches the unique name of the event
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Fetches the model for the static bag of payload data the event expects
     *
     * @return FixedStaticBagModelNode
     */
    public function getPayloadStaticBagModel()
    {
        return $this->payloadStaticBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->payloadStaticBagModelNode->validate($subValidationContext);
    }
}
