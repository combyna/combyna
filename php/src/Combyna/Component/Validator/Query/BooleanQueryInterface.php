<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Query;

/**
 * Interface BooleanQueryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BooleanQueryInterface extends QueryInterface
{
    /**
     * Fetches the result to return if this query cannot be handled
     * by any of the validation contexts in the chain
     *
     * @return bool
     */
    public function getDefaultResult();
}
