<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Constraint;

/**
 * Class AssuredStaticExistsConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredStaticExistsConstraint implements ExpressionValidationConstraintInterface
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
     * Fetches the name of the assured static being referenced
     *
     * @return string
     */
    public function getStaticName()
    {
        return $this->staticName;
    }
}
