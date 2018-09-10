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
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class UnknownFunctionTypeNode
 *
 * Indicates that a referenced library exists but the type given for the function is not supported
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFunctionTypeNode extends AbstractActNode implements FunctionNodeInterface
{
    const TYPE = 'unknown-function-type';

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $functionType;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $functionName
     * @param string $functionType
     */
    public function __construct($libraryName, $functionName, $functionType)
    {
        $this->functionName = $functionName;
        $this->functionType = $functionType;
        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                sprintf(
                    'Function "%s.%s" type "%s" is not supported',
                    $this->libraryName,
                    $this->functionName,
                    $this->functionType
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->functionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnTypeDeterminer()
    {
        // We don't know what the function's return type could be as it is not defined
        return new PresolvedTypeDeterminer(new UnresolvedType('undefined function type'));
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        // Nothing to do - validation of this node itself will always fail
    }
}
