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
 * Interface ConstraintValidatorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConstraintValidatorInterface
{
    /**
     * Fetches a map from the FQCN of the classes of constraint that this validator is for
     * to the public method that may be called to validate them
     *
     * @return callable[]
     */
    public function getConstraintClassToValidatorCallableMap();

    /**
     * Fetches the classes of the registered passes that will be applied to the behaviour spec
     *
     * @return string[]
     */
    public function getPassClasses();
}
