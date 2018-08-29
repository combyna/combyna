<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Constraint;

/**
 * Class KnownFailureConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownFailureConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $failureDescription;

    /**
     * @param string $failureDescription A description of the failure
     */
    public function __construct($failureDescription)
    {
        $this->failureDescription = $failureDescription;
    }

    /**
     * Fetches the callback
     *
     * @return callable
     */
    public function getFailureDescription()
    {
        return $this->failureDescription;
    }
}
