<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;

/**
 * Interface SubValidationContextInterface
 *
 * Encapsulates a part of the context during validation,
 * like the current event or the current store
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SubValidationContextInterface
{
    /**
     * Fetches the ACT node this context represents
     *
     * @return ActNodeInterface
     */
    public function getActNode();

    /**
     * Fetches the behaviour spec for the ACT node this context represents
     *
     * @return BehaviourSpecInterface
     */
    public function getBehaviourSpec();

    /**
     * Fetches the parent sub-validation context
     *
     * @return SubValidationContextInterface|null
     */
    public function getParentContext();

    /**
     * Builds the path to this validation context in the expression tree
     *
     * @return string
     */
    public function getPath();

    /**
     * Fetches a map from the class of each type of query supported to a callable that can handle it
     *
     * @return callable[]
     */
    public function getQueryClassToQueryCallableMap();
}
