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

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Interface ConstraintInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConstraintInterface
{
    /**
     * Determines whether the validation of this constraint makes the specified query directly
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return bool
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier);
}
