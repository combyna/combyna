<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Interface FixedStaticDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the expression evaluated as the default value for this static, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getDefaultExpression();

    /**
     * Fetches the name for this static in its bag
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the resolved type for the static
     *
     * @return TypeInterface
     */
    public function getResolvedStaticType();

    /**
     * Fetches the type that a value of this static must match
     *
     * @return TypeDeterminerInterface
     */
    public function getStaticTypeDeterminer();

    /**
     * Determines whether this static must be defined in the bag or not
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Checks that the provided expression evaluates to a static
     * that is compatible with this definition's type
     *
     * @param ExpressionNodeInterface $expressionNode
     * @param ValidationContextInterface $validationContext
     * @param string $contextDescription
     */
    public function validateExpression(
        ExpressionNodeInterface $expressionNode,
        ValidationContextInterface $validationContext,
        $contextDescription
    );
}
