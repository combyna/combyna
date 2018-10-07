<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class WidgetHasValueConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetHasValueConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $valueName;

    /**
     * @param string $valueName
     */
    public function __construct($valueName)
    {
        $this->valueName = $valueName;
    }

    /**
     * Fetches the name of the value to check for existence of
     *
     * @return string
     */
    public function getValueName()
    {
        return $this->valueName;
    }
}
