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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

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

    /**
     * Determines a type for the current validation context
     *
     * @param TypeDeterminerInterface $typeDeterminer
     * @return TypeInterface
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer);
}
