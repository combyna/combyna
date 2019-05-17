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
     * Creates a "determined" definition from this node, where the type has been resolved
     *
     * @param ValidationContextInterface $validationContext
     * @return DeterminedFixedStaticDefinitionInterface
     */
    public function determine(ValidationContextInterface $validationContext);

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
     * Fetches the resolved type for the static. In development mode,
     * validation will have run and so this is guaranteed to return the true type.
     * In production mode, validation will not have run and so the true type may not
     * have been resolved (eg. if determining it involves a validation query) -
     * in that scenario, an Any type will be returned.
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
