<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\FunctionExpression;
use Combyna\Component\Expression\Validation\Constraint\ValidFunctionCallConstraint;
use Combyna\Component\Expression\Validation\Query\FunctionReturnTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class FunctionExpressionNode
 *
 * Calls a library function and returns its result
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionExpressionNode extends AbstractExpressionNode
{
    const TYPE = FunctionExpression::TYPE;

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
     * @param ExpressionBagNode $argumentExpressionBag
     */
    public function __construct(
        $libraryName,
        $functionName,
        ExpressionBagNode $argumentExpressionBag
    ) {
        $this->argumentExpressionBagNode = $argumentExpressionBag;
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Ensure all argument expressions are valid within themselves
        $specBuilder->addChildNode($this->argumentExpressionBagNode);

        // Now ensure all argument expressions resolve to valid static types for their corresponding parameters
        $specBuilder->addConstraint(
            new ValidFunctionCallConstraint(
                $this->libraryName,
                $this->functionName,
                $this->argumentExpressionBagNode
            )
        );
    }

    /**
     * Fetches the argument expression bag
     *
     * @return ExpressionBagNode
     */
    public function getArgumentExpressionBag()
    {
        return $this->argumentExpressionBagNode;
    }

    /**
     * Fetches the name of the function
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * Fetches the name of the library that defines the function
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(
            new FunctionReturnTypeQuery($this->libraryName, $this->functionName),
            $this
        );
    }
}
