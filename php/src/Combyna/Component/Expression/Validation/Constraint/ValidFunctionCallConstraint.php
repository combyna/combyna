<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;

/**
 * Class ValidFunctionCallConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidFunctionCallConstraint implements ExpressionValidationConstraintInterface
{
    /**
     * @var ExpressionBagNode
     */
    private $argumentExpressionBagNode;

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $functionName
     * @param ExpressionBagNode $argumentExpressionBagNode
     */
    public function __construct(
        $libraryName,
        $functionName,
        ExpressionBagNode $argumentExpressionBagNode
    ) {
        $this->argumentExpressionBagNode = $argumentExpressionBagNode;
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the bag of expression nodes to be used as arguments to the function
     *
     * @return ExpressionBagNode
     */
    public function getArgumentExpressionBag()
    {
        return $this->argumentExpressionBagNode;
    }

    /**
     * Fetches the name of the function being referenced
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * Fetches the name of the library that should define the function being referenced
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
