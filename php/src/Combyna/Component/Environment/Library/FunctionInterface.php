<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface FunctionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FunctionInterface
{
    /**
     * Calls the function, passing it its arguments evaluated to static values,
     * and returns the static value it evaluates to
     *
     * @param StaticBagInterface $argumentStaticBag
     * @return StaticInterface
     */
    public function call(StaticBagInterface $argumentStaticBag);

    /**
     * Fetches the name of this function, which must be unique within its library
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
