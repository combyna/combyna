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
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class DynamicUnknownFunctionNode
 *
 * Indicates that a referenced library exists but does not define the specified function
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DynamicUnknownFunctionNode extends AbstractActNode implements FunctionNodeInterface, DynamicActNodeInterface
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
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($libraryName, $functionName, QueryRequirementInterface $queryRequirement)
    {
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;

        // Apply the validation for this dynamically created ACT node
        $queryRequirement->adoptDynamicActNode($this);
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
    public function getReturnTypeDeterminer(ActNodeInterface $nodeQueriedFrom)
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
