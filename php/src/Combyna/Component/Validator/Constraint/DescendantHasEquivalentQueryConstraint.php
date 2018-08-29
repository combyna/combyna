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
 * Class DescendantHasEquivalentQueryConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DescendantHasEquivalentQueryConstraint implements ConstraintInterface
{
    /**
     * @var QuerySpecifierInterface
     */
    private $querySpecifier;

    /**
     * @param QuerySpecifierInterface $querySpecifier
     */
    public function __construct(QuerySpecifierInterface $querySpecifier)
    {
        $this->querySpecifier = $querySpecifier;
    }

    /**
     * Fetches the query specifier
     *
     * @return QuerySpecifierInterface
     */
    public function getQuerySpecifier()
    {
        return $this->querySpecifier;
    }
}
