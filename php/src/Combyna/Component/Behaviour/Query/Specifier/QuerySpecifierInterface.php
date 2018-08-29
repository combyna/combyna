<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Query\Specifier;

/**
 * Interface QuerySpecifierInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QuerySpecifierInterface
{
    /**
     * Fetches a description of the type of query, for use in violation messages
     *
     * @return string
     */
    public function getDescription();
}
