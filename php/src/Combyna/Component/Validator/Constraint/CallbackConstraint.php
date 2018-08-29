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
 * Class CallbackConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CallbackConstraint implements ConstraintInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Fetches the callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
