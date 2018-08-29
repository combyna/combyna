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
 * Class BehaviourSpecTreeWalker
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BehaviourSpecTreeWalker implements BehaviourSpecTreeWalkerInterface
{
    /**
     * {@inheritdoc}
     */
    public function walk(
        BehaviourSpecInterface $spec,
        array $nodeClassToVisitorCallablesMap,
        BehaviourSpecInterface $rootSpec
    ) {
        foreach ($spec->getChildSpecs() as $childSpec) {
            $childNode = $childSpec->getSubjectOwnerNode();
            $childNodeClass = get_class($childNode);

            if (array_key_exists($childNodeClass, $nodeClassToVisitorCallablesMap)) {
                $nodeClassToVisitorCallablesMap[$childNodeClass]($childNode, $spec, $rootSpec);
            }

            $this->walk($childSpec, $nodeClassToVisitorCallablesMap, $rootSpec);
        }
    }
}
