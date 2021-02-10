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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class UnknownFunctionNode
 *
 * Indicates that a referenced library exists but does not define the specified function
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFunctionNode extends AbstractActNode implements DynamicActNodeInterface, FunctionNodeInterface
{
    const TYPE = 'unknown-function';

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
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($libraryName, $functionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                'Library "' . $this->libraryName .
                '" does not support function "' .
                $this->functionName . '"'
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
        return new PresolvedTypeDeterminer(new UnresolvedType('undefined function'));
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
