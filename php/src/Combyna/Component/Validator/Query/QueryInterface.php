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

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Interface QueryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryInterface
{
    /**
     * Fetches a description of the query, for use in violation messages
     *
     * @return string
     */
    public function getDescription();

    /**
     * Determines whether this query or an argument of it makes the specified query
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return bool
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier);
}
