<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator;

/**
 * Interface ViolationInterface
 *
 * Represents a failure to meet a validation constraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViolationInterface
{
    /**
     * Returns a human-readable description of the violation
     *
     * @return string
     */
    public function getDescription();

    /**
     * Fetches the path to the expression where the violation occurred
     *
     * @return string
     */
    public function getPath();
}
