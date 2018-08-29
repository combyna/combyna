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
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface EventDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the unique name of the event
     *
     * @return string
     */
    public function getEventName();

    /**
     * Fetches the model for the static bag of payload data the event expects
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getPayloadStaticBagModel();

    /**
     * Fetches the type of a static inside the payload data the event expects
     *
     * @param string $payloadStaticName
     * @return TypeInterface
     */
    public function getPayloadStaticType($payloadStaticName);

    /**
     * Returns whether or not this event definition is defined
     *
     * @return bool
     */
    public function isDefined();
}
