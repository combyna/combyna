<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoter;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface AssuranceNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AssuranceNodeInterface extends ActNodeInterface
{
    /**
     * Determines whether this assurance defines a static with the given name
     *
     * @param string $staticName
     * @return bool
     */
    public function definesStatic($staticName);

    /**
     * Fetches the constraint for this assurance type (one of the constants)
     *
     * @return string
     */
    public function getConstraint();

    /**
     * Fetches the names of any and all assured statics that this assurance will define
     * that must be referenced by an AssuredExpression
     *
     * @return string[]
     */
    public function getRequiredAssuredStaticNames();

    /**
     * Fetches the type that a static this assurance defines must evaluate to
     *
     * @param ValidationContextInterface $validationContext
     * @param string $assuredStaticName
     * @return TypeInterface
     */
    public function getStaticType(ValidationContextInterface $validationContext, $assuredStaticName);

    /**
     * Promotes this node to an actual Assurance
     *
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @return AssuranceInterface
     */
    public function promote(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionNodePromoter $expressionNodePromoter
    );

    /**
     * Checks that all operands for this assurance validate recursively and that they will only
     * resolve to the expected types of static expression
     *
     * @param ValidationContextInterface $validationContext
     */
    public function validate(ValidationContextInterface $validationContext);
}
