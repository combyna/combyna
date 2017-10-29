<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface EventDefinitionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionInterface
{
    /**
     * Checks that the provided payload static bag matches the model set for this event definition,
     * raising an error if it does not
     *
     * @param StaticBagInterface $payloadStaticBag
     * @throws FixedStaticBagModelMismatchException
     */
    public function assertValidPayloadStaticBag(StaticBagInterface $payloadStaticBag);

    /**
     * Fetches the unique name of the library that defines the event type
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name for the event type within the system
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the model that payload static bags for this event must match
     *
     * @return FixedStaticBagModelInterface
     */
    public function getPayloadStaticBagModel();
}
