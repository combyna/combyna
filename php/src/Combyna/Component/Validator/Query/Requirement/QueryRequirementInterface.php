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

use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Interface QueryRequirementInterface
 *
 * Passed into getter methods for definition ACT nodes (eg. "get widget definition from library")
 * in order to allow any dynamically-created ones (usually for "unknown" or "invalid" definitions)
 * to be added to the ACT.
 *
 * Inside a non-definition ACT node class, create a DynamicContainerNode, add it as a child
 * and dynamically add children to that rather than passing in a QueryRequirement.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryRequirementInterface extends DynamicActNodeAdopterInterface
{
    /**
     * Determines a type for the current validation context
     *
     * @deprecated We should never be resolving a type from where a definition
     *             was used (eg. a widget using a widget definition), only from
     *             where it was defined (eg. the LibraryNode) which should add it
     *             to its DynamicContainerNode.
     *
     * @param TypeDeterminerInterface $typeDeterminer
     * @return TypeInterface
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer);
}
