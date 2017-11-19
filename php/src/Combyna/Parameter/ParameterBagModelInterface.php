<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Parameter;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Parameter\Exception\InvalidArgumentException;

/**
 * Interface ParameterBagModelInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParameterBagModelInterface
{
    /**
     * Checks that the static is defined and matches its type for this model
     *
     * @param string $name
     * @param StaticInterface $value
     * @throws InvalidArgumentException Throws when the static does not match
     */
    public function assertValidArgument($name, StaticInterface $value);

    /**
     * Checks that all statics in the provided bag are defined and match their types for this model
     *
     * @param StaticBagInterface $staticBag
     * @throws InvalidArgumentException Throws when any static does not match or is missing
     */
    public function assertValidArgumentBag(StaticBagInterface $staticBag);

    /**
     * Checks that all expressions in the provided bag evaluate to statics that match
     * the types for their corresponding parameters, and that there are no extra arguments
     * with no matching parameter or required parameters that are missing an argument in the bag
     *
     * @param ValidationContextInterface $validationContext
     * @param ExpressionBagNode $expressionBagNode
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    );
}
