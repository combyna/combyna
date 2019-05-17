<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface QueryNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the expression to evaluate for the result of this query
     *
     * @return ExpressionNodeInterface
     */
    public function getExpression();

    /**
     * Fetches the unique name of this query within its store
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the model for parameters to this query
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getParameterBagModel();

    /**
     * Validates that this query will be able to execute correctly with the provided query arguments
     *
     * @param ValidationContextInterface $validationContext
     * @param ExpressionBagNode $expressionBagNode
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    );
}
