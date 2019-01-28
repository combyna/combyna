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
 * Interface BehaviourSpecModifierInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecModifierInterface
{
    /**
     * Performs the modifications to the behaviour spec
     *
     * @param BehaviourSpecBuilderInterface $specBuilder
     */
    public function modifySpecBuilder(BehaviourSpecBuilderInterface $specBuilder);
}
