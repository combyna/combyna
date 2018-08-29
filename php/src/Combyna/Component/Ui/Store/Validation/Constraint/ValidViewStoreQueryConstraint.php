<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\Validation\Constraint\ExpressionValidationConstraintInterface;

/**
 * Class ValidViewStoreQueryConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidViewStoreQueryConstraint implements ExpressionValidationConstraintInterface
{
    /**
     * @var ExpressionBagNode
     */
    private $argumentExpressionBagNode;

    /**
     * @var string
     */
    private $queryName;

    /**
     * @param string $queryName
     * @param ExpressionBagNode $argumentExpressionBagNode
     */
    public function __construct(
        $queryName,
        ExpressionBagNode $argumentExpressionBagNode
    ) {
        $this->argumentExpressionBagNode = $argumentExpressionBagNode;
        $this->queryName = $queryName;
    }

    /**
     * Fetches the bag of expression nodes to be used as arguments to the query
     *
     * @return ExpressionBagNode
     */
    public function getArgumentExpressionBag()
    {
        return $this->argumentExpressionBagNode;
    }

    /**
     * Fetches the name of the query being referenced
     *
     * @return string
     */
    public function getQueryName()
    {
        return $this->queryName;
    }
}
