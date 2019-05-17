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
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface SignalDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the name of the library this definition belongs to
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the model for the static bag of payload data the signal expects
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getPayloadStaticBagModel();

    /**
     * Fetches the type of the specified static for this signal's payload
     *
     * @param string $staticName
     * @return TypeInterface
     */
    public function getPayloadStaticType($staticName);

    /**
     * Fetches the unique name of the signal
     *
     * @return string
     */
    public function getSignalName();

    /**
     * Determines whether signals of this definition should be broadcast externally
     *
     * @return bool
     */
    public function isBroadcast();

    /**
     * Returns whether or not this signal definition is defined
     *
     * @return bool
     */
    public function isDefined();
}
