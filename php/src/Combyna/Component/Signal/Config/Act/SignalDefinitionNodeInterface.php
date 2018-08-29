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
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Interface SignalDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the model for the static bag of payload data the signal expects
     *
     * @return FixedStaticBagModelNode
     */
    public function getPayloadStaticBagModel();

    /**
     * Fetches the type of the specified static for this signal's payload
     *
     * @param string $staticName
     * @param QueryRequirementInterface $queryRequirement
     * @return TypeInterface
     */
    public function getPayloadStaticType($staticName, QueryRequirementInterface $queryRequirement);

    /**
     * Fetches the unique name of the signal
     *
     * @return string
     */
    public function getSignalName();

    /**
     * Returns whether or not this signal definition is defined
     *
     * @return bool
     */
    public function isDefined();
}
