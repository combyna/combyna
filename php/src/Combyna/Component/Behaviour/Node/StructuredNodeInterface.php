<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Node;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;

/**
 * Interface StructuredNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StructuredNodeInterface
{
    /**
     * Uses the provided builder to construct a BehaviourSpec with all constraints to apply
     * and structured child nodes that may be validated recursively
     *
     * @param BehaviourSpecBuilderInterface $specBuilder
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder);

    /**
     * Determines whether this node makes the specified query directly
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return bool
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier);
}
