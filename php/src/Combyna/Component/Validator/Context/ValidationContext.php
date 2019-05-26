<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Behaviour\BehaviourFactoryInterface;
use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Behaviour\Validation\Validator\BehaviourSpecValidatorInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\Factory\DelegatingSubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;
use Combyna\Component\Validator\Query\Requirement\ActNodeQueryRequirement;
use Combyna\Component\Validator\Query\Requirement\BooleanQueryRequirement;
use Combyna\Component\Validator\Query\Requirement\TypeQueryRequirement;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;
use Combyna\Component\Validator\ViolationInterface;
use LogicException;

/**
 * Class ValidationContext
 *
 * Represents a current state during validation, tracking any violations
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidationContext implements ValidationContextInterface
{
    /**
     * @var BehaviourFactoryInterface
     */
    private $behaviourFactory;

    /**
     * @var BehaviourSpecValidatorInterface
     */
    private $behaviourSpecValidator;

    /**
     * @var RootValidationContextInterface
     */
    private $rootValidationContext;

    /**
     * @var SubValidationContextInterface
     */
    private $subValidationContext;

    /**
     * @var DelegatingSubValidationContextFactoryInterface
     */
    private $subValidationContextFactory;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param BehaviourFactoryInterface $behaviourFactory
     * @param DelegatingSubValidationContextFactoryInterface $subValidationContextFactory
     * @param BehaviourSpecValidatorInterface $behaviourSpecValidator
     * @param RootValidationContextInterface $rootValidationContext
     * @param SubValidationContextInterface $subValidationContext
     */
    public function __construct(
        ValidationFactoryInterface $validationFactory,
        BehaviourFactoryInterface $behaviourFactory,
        DelegatingSubValidationContextFactoryInterface $subValidationContextFactory,
        BehaviourSpecValidatorInterface $behaviourSpecValidator,
        RootValidationContextInterface $rootValidationContext,
        SubValidationContextInterface $subValidationContext
    ) {
        $this->behaviourFactory = $behaviourFactory;
        $this->behaviourSpecValidator = $behaviourSpecValidator;
        $this->rootValidationContext = $rootValidationContext;
        $this->subValidationContext = $subValidationContext;
        $this->subValidationContextFactory = $subValidationContextFactory;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addDivisionByZeroViolation()
    {
        $this->rootValidationContext->addDivisionByZeroViolation($this->subValidationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function addGenericViolation($description)
    {
        $this->rootValidationContext->addGenericViolation($description, $this->subValidationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription
    ) {
        $this->rootValidationContext->addTypeMismatchViolation(
            $expectedType,
            $actualType,
            $contextDescription,
            $this->subValidationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addViolation(ViolationInterface $violation)
    {
        $this->rootValidationContext->addViolation($violation);
    }

    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode)
    {
        $this->rootValidationContext->adoptDynamicActNode($actNode, $this->subValidationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createActNodeQueryRequirement(ActNodeQueryInterface $query, ActNodeInterface $nodeToQueryFrom)
    {
        // TODO: Simplify this. Note that `$nodeToQueryFrom` is not necessarily the same as `$this->getActNode()`
        $subValidationContext = $this->rootValidationContext
            ->getRootSubValidationContext()
            ->getBehaviourSpec()
            ->getSubValidationContextForDescendant(
                $nodeToQueryFrom,
                $this->rootValidationContext->getRootSubValidationContext()
            );

        return new ActNodeQueryRequirement($query, $this->rootValidationContext, $subValidationContext, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function createBooleanQueryRequirement(BooleanQueryInterface $query)
    {
        return new BooleanQueryRequirement($query, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubContext(
        SubValidationContextSpecifierInterface $subContextSpecifier,
        StructuredNodeInterface $structuredNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return $this->subValidationContextFactory->createContext(
            $subContextSpecifier,
            $this->subValidationContext,
            $structuredNode,
            $behaviourSpec,
            $this->subValidationContext->getSubjectActNode() // Carry the subject ACT node forward
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createTypeQueryRequirement(ResultTypeQueryInterface $query)
    {
        return new TypeQueryRequirement($query, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->subValidationContext->getCurrentActNode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentParentActNode()
    {
        $parentSubValidationContext = $this->subValidationContext->getParentContext();

        if ($parentSubValidationContext === null) {
            throw new LogicException('Sub-validation context has no parent');
        }

        return $parentSubValidationContext->getCurrentActNode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantSpecsWithQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->rootValidationContext->getDescendantSpecsWithQuery(
            $querySpecifier,
            $this->subValidationContext->getBehaviourSpec()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionResultType(ExpressionNodeInterface $expressionNode)
    {
        return $this->rootValidationContext->getExpressionResultType($expressionNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->subValidationContext->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActNode()
    {
        return $this->subValidationContext->getSubjectActNode();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubValidationContext()
    {
        return $this->subValidationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function queryForActNode(
        ActNodeQueryInterface $actNodeQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        return $this->rootValidationContext->queryForActNode(
            $actNodeQuery,
            $nodeToQueryFrom
        );
    }

    /**
     * {@inheritdoc}
     */
    public function queryForBoolean(
        BooleanQueryInterface $booleanQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        return $this->rootValidationContext->queryForBoolean(
            $booleanQuery,
            $nodeToQueryFrom
        );
    }

    /**
     * {@inheritdoc}
     */
    public function queryForResultType(
        ResultTypeQueryInterface $resultTypeQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        return $this->rootValidationContext->queryForResultType(
            $resultTypeQuery,
            $nodeToQueryFrom
        );
    }

    /**
     * {@inheritdoc}
     */
    public function throwIfViolated()
    {
        $this->rootValidationContext->throwIfViolated();
    }

    /**
     * {@inheritdoc}
     */
    public function validateActNodeInIsolation(ActNodeInterface $actNode)
    {
        return $this->rootValidationContext->validateActNodeInIsolation($actNode);
    }

    /**
     * {@inheritdoc}
     */
    public function wrapInValuedType(TypeInterface $type, ExpressionNodeInterface $expressionNode)
    {
        return $this->rootValidationContext->wrapInValuedType($type, $expressionNode);
    }

    /**
     * {@inheritdoc}
     */
    public function wrapInValuedTypeIfPureExpression(TypeInterface $type, ExpressionNodeInterface $expressionNode)
    {
        return $this->rootValidationContext->wrapInValuedTypeIfPureExpression($type, $expressionNode);
    }
}
