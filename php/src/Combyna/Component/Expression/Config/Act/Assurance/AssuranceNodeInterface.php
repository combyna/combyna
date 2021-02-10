<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Interface AssuranceNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AssuranceNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the name of the static this assurance defines,
     * which must be referenced by an AssuredExpression
     *
     * @return string
     */
    public function getAssuredStaticName();

    /**
     * Fetches a determiner for the type that a static this assurance defines must evaluate to
     *
     * @return TypeDeterminerInterface
     */
    public function getAssuredStaticTypeDeterminer();

    /**
     * Fetches the constraint for this assurance type (one of the constants)
     *
     * @return string
     */
    public function getConstraint();

    /**
     * Fetches the input expression to be assured
     *
     * @return ExpressionNodeInterface
     */
    public function getInputExpression();
}
