<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface FunctionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FunctionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the unique name of the function within its library
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the type of static that this function will return
     *
     * @return TypeInterface
     */
    public function getReturnType();

    /**
     * Checks that all expressions in the bag only ever evaluate to valid values for
     * their corresponding parameters and that there are no extra or missing arguments for parameters
     *
     * @param ValidationContextInterface $validationContext
     * @param ExpressionBagNode $expressionBagNode
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    );
}
