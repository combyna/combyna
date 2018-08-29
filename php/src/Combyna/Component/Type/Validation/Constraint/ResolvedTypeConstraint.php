<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Validation\Constraint;

use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class ResolvedTypeConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResolvedTypeConstraint implements ConstraintInterface
{
    /**
     * @var TypeInterface
     */
    private $type;

    /**
     * @param TypeInterface $type
     */
    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * Fetches the type
     *
     * @return TypeInterface
     */
    public function getType()
    {
        return $this->type;
    }
}
