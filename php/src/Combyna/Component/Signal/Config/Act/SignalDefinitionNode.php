<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class SignalDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionNode extends AbstractActNode
{
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
     */
    public function __construct($signalName, FixedStaticBagModelNode $payloadStaticBagModelNode)
    {
        $this->payloadStaticBagModelNode = $payloadStaticBagModelNode;
        $this->signalName = $signalName;
    }

    /**
     * Fetches the model for the static bag of payload data the signal expects
     *
     * @return FixedStaticBagModelNode
     */
    public function getPayloadStaticBagModel()
    {
        return $this->payloadStaticBagModelNode;
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
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->payloadStaticBagModelNode->validate($subValidationContext);
    }
}
