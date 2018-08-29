<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Query\Requirement;

use Combyna\Component\Config\Act\DynamicActNodeInterface;

/**
 * Interface QueryRequirementInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryRequirementInterface
{
    /**
     * Applies the validation for the provided dynamically-created ACT node
     *
     * @param DynamicActNodeInterface $actNode
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode);
}
