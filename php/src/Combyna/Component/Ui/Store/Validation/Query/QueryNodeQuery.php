<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;

/**
 * Class QueryNodeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueryNodeQuery implements ActNodeQueryInterface
{
    /**
     * @var string
     */
    private $queryName;

    /**
     * @param string $queryName
     */
    public function __construct($queryName)
    {
        $this->queryName = $queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The ACT node of the view store query "' . $this->queryName . '"';
    }

    /**
     * Fetches the name of the query
     *
     * @return string
     */
    public function getQueryName()
    {
        return $this->queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
