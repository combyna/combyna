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
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;

/**
 * Class UnknownLibraryForFunctionNode
 *
 * Indicates that a referenced library (and therefore also the specified function) do not exist
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownLibraryForFunctionNode extends AbstractActNode implements DynamicActNodeInterface, FunctionNodeInterface
{
    const TYPE = 'unknown-library-and-function';

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
                'Neither the library "' . $this->libraryName .
                '" nor its function "' . $this->functionName . '" exist'
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
        // Library and function are both unknown, so we don't know
        // what the function's return type could be
        return new UnresolvedTypeDeterminer('unresolved library and function');
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
