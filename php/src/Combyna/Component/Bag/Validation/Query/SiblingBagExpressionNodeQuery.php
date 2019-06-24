<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;

/**
 * Class SiblingBagExpressionNodeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SiblingBagExpressionNodeQuery implements ActNodeQueryInterface
{
    /**
     * @var string
     */
    private $expressionName;

    /**
     * @param string $expressionName
     */
    public function __construct($expressionName)
    {
        $this->expressionName = $expressionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf('The expression node for the sibling expression "%s"', $this->expressionName);
    }

    /**
     * Fetches the name of the expression to fetch the node of
     *
     * @return string
     */
    public function getExpressionName()
    {
        return $this->expressionName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
