<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Spec;

/**
 * Interface BehaviourSpecTreeWalkerInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecTreeWalkerInterface
{
    /**
     * Walks the provided tree of BehaviourSpecs, calling the relevant visitor
     * in the provided map for each matching node
     *
     * @param BehaviourSpecInterface $spec
     * @param callable[] $nodeClassToVisitorCallablesMap
     * @param BehaviourSpecInterface $rootSpec
     */
    public function walk(
        BehaviourSpecInterface $spec,
        array $nodeClassToVisitorCallablesMap,
        BehaviourSpecInterface $rootSpec
    );
}
