<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\FixedStaticBagModelInterface;

/**
 * Class SignalDefinition
 *
 * Defines the name and payload structure for an event that could occur
 * or a request that could be made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinition implements SignalDefinitionInterface
{
    /**
     * A unique name for the signal type within the system
     *
     * @var string
     */
    private $name;

    /**
     * Defines the statics that must be provided as the payload to go along with the signal
     *
     * @var FixedStaticBagModelInterface
     */
    private $payloadStaticBagModel;

    /**
     * @param string $name
     * @param FixedStaticBagModelInterface $payloadStaticBagModel
     */
    public function __construct($name, FixedStaticBagModelInterface $payloadStaticBagModel)
    {
        $this->name = $name;
        $this->payloadStaticBagModel = $payloadStaticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
