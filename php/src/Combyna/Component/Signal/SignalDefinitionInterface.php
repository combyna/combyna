<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface SignalDefinitionInterface
 *
 * Defines the name and payload structure for an event that could occur
 * or a request that could be made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionInterface
{
    /**
     * Checks that the provided payload static bag matches the model set for this signal definition,
     * raising an error if it does not
     *
     * @param StaticBagInterface $payloadStaticBag
     * @throws FixedStaticBagModelMismatchException
     */
    public function assertValidPayloadStaticBag(StaticBagInterface $payloadStaticBag);

    /**
     * Fetches the unique name of the library that defines this signal definition
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name for the signal type within the system
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the model that payload static bags for this signal must match
     *
     * @return FixedStaticBagModelInterface
     */
    public function getPayloadStaticBagModel();
}
