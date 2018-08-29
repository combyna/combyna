<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Compiler;

use Combyna\Component\Behaviour\Compiler\Pass\BehaviourSpecPassInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;

/**
 * Interface BehaviourSpecTreeCompilerInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecTreeCompilerInterface
{
    /**
     * Registers a pass to be run against each behaviour spec tree
     *
     * @param BehaviourSpecPassInterface $behaviourSpecPass
     */
    public function addSpecPass(BehaviourSpecPassInterface $behaviourSpecPass);

    /**
     * Processes a behaviour spec with all passes referenced by constraints inside it
     *
     * @param BehaviourSpecInterface $rootSpec
     */
    public function compile(BehaviourSpecInterface $rootSpec);
}
