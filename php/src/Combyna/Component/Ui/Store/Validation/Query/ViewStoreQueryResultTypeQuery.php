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
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class ViewStoreQueryResultTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreQueryResultTypeQuery implements ResultTypeQueryInterface
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
        return 'The result type of the view store query "' . $this->queryName . '"';
    }

    /**
     * Fetches the name of the payload static to query the result type of
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
