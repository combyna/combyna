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
 * Class CompoundWidgetDefinitionHasAttributeConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinitionHasAttributeConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $attributeName;

    /**
     * @param string $attributeName
     */
    public function __construct($attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     * Fetches the name of the attribute to check for existence of
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }
}
