<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Constraint;

use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class SignalDefinitionHasPayloadStaticConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionHasPayloadStaticConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $staticName;

    /**
     * @param string $staticName
     */
    public function __construct($staticName)
    {
        $this->staticName = $staticName;
    }

    /**
     * Fetches the name of the payload static to check for existence of
     *
     * @return string
     */
    public function getPayloadStaticName()
    {
        return $this->staticName;
    }
}
