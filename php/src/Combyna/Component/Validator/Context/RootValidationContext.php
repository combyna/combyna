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
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Behaviour\Validation\Validator\BehaviourSpecValidatorInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Config\Act\DynamicUnknownNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;
use Combyna\Component\Validator\Query\QueryInterface;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;
use Combyna\Component\Validator\ViolationInterface;
use LogicException;

/**
 * Class RootValidationContext
 *
 * Represents a current state during validation, tracking any violations
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootValidationContext implements RootValidationContextInterface
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
     * @var BehaviourSpecInterface
     */
    private $rootNodeBehaviourSpec;

    /**
     * @var RootSubValidationContextInterface
     */
    private $rootSubValidationContext;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @var ViolationInterface[]
     */
    private $violations = [];

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param BehaviourFactoryInterface $behaviourFactory
     * @param RootSubValidationContextInterface $rootSubValidationContext
     * @param BehaviourSpecInterface $rootNodeBehaviourSpec
     * @param BehaviourSpecValidatorInterface $behaviourSpecValidator
     */
    public function __construct(
        ValidationFactoryInterface $validationFactory,
        BehaviourFactoryInterface $behaviourFactory,
        RootSubValidationContextInterface $rootSubValidationContext,
        BehaviourSpecInterface $rootNodeBehaviourSpec,
        BehaviourSpecValidatorInterface $behaviourSpecValidator
    ) {
        $this->behaviourFactory = $behaviourFactory;
        $this->behaviourSpecValidator = $behaviourSpecValidator;
        $this->rootNodeBehaviourSpec = $rootNodeBehaviourSpec;
        $this->rootSubValidationContext = $rootSubValidationContext;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addDivisionByZeroViolation(SubValidationContextInterface $subValidationContext)
    {
        $this->addViolation($this->validationFactory->createDivisionByZeroViolation($subValidationContext));
    }

    /**
     * {@inheritdoc}
     */
    public function addGenericViolation($description, SubValidationContextInterface $subValidationContext)
    {
        $this->addViolation(
            $this->validationFactory->createGenericViolation(
                $description,
                $subValidationContext
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription,
        SubValidationContextInterface $subValidationContext
    ) {
        $this->addViolation(
            $this->validationFactory->createTypeMismatchViolation(
                $expectedType,
                $actualType,
                $subValidationContext,
                $contextDescription
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addViolation(ViolationInterface $violation)
    {
        $this->violations[] = $violation;
    }

    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode, SubValidationContextInterface $subValidationContext)
    {
        $behaviourSpecBuilder = $this->behaviourFactory->createBehaviourSpecBuilder($actNode);
        $actNode->buildBehaviourSpec($behaviourSpecBuilder);
        $behaviourSpec = $behaviourSpecBuilder->build();

        $this->behaviourSpecValidator->validateSpec(
            $behaviourSpec,
            $this,
            $subValidationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantSpecsWithQuery(
        QuerySpecifierInterface $querySpecifier,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return $behaviourSpec->getDescendantSpecsWithQuery($querySpecifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionResultType(ExpressionNodeInterface $expressionNode)
    {
        $subValidationContext = $this->rootNodeBehaviourSpec
            ->getSubValidationContextForDescendant(
                $expressionNode,
                $this->rootSubValidationContext
            );

        if (!$subValidationContext) {
            throw new LogicException('Could not find the expression node in the behaviour spec trees');
        }

        $validationContext = $this->validationFactory->createContext(
            $this,
            $subValidationContext,
            $this->behaviourSpecValidator
        );

        return $expressionNode->getResultTypeDeterminer()->determine($validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootSubValidationContext()
    {
        return $this->rootSubValidationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function queryForActNode(
        ActNodeQueryInterface $actNodeQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        $subValidationContext = $this->rootNodeBehaviourSpec
            ->getSubValidationContextForDescendant(
                $nodeToQueryFrom,
                $this->rootSubValidationContext
            );

        $resultActNode = $this->query($actNodeQuery, $subValidationContext);

        if ($resultActNode !== null) {
            // A sub-context somewhere in the chain handled the query
            return $resultActNode;
        }

        // Nothing was able to handle the ACT node query - fail validation
        $this->addGenericViolation(
            'No sub-validation context was able to handle the ACT node query: ' . $actNodeQuery->getDescription(),
            $subValidationContext
        );

        $validationContext = $this->validationFactory->createContext(
            $this,
            $subValidationContext,
            $this->behaviourSpecValidator
        );

        return new DynamicUnknownNode(
            $actNodeQuery->getDescription(),
            $validationContext->createActNodeQueryRequirement($actNodeQuery, $nodeToQueryFrom)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function queryForBoolean(
        BooleanQueryInterface $booleanQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        $subValidationContext = $this->rootNodeBehaviourSpec
            ->getSubValidationContextForDescendant(
                $nodeToQueryFrom,
                $this->rootSubValidationContext
            );

        $resultBoolean = $this->query($booleanQuery, $subValidationContext);

        // If nothing was available to handle the boolean query, return its default result
        return $resultBoolean === null ? $booleanQuery->getDefaultResult() : $resultBoolean;
    }

    /**
     * {@inheritdoc}
     */
    public function queryForResultType(
        ResultTypeQueryInterface $resultTypeQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        $subValidationContext = $this->rootNodeBehaviourSpec
            ->getSubValidationContextForDescendant(
                $nodeToQueryFrom,
                $this->rootSubValidationContext
            );

        $resultType = $this->query($resultTypeQuery, $subValidationContext);

        if ($resultType !== null) {
            // A sub-context somewhere in the chain handled the query
            return $resultType;
        }

        // Nothing was able to handle the type query - fail validation
        $this->addGenericViolation(
            'No sub-validation context was able to handle the result type query: ' . $resultTypeQuery->getDescription(),
            $subValidationContext
        );

        return new UnresolvedType($resultTypeQuery->getDescription());
    }

    /**
     * @param QueryInterface $query
     * @param SubValidationContextInterface $subValidationContext
     * @return mixed|null
     */
    private function query(
        QueryInterface $query,
        SubValidationContextInterface $subValidationContext
    ) {
        $nodeQueriedFrom = $subValidationContext->getCurrentActNode();
        $queryClass = get_class($query);

        do {
            $queryClassToQueryCallableMap = $subValidationContext->getQueryClassToQueryCallableMap();

            if (array_key_exists($queryClass, $queryClassToQueryCallableMap)) {
                $validationContext = $this->validationFactory->createContext(
                    $this,
                    $subValidationContext,
                    $this->behaviourSpecValidator
                );

                $result = $queryClassToQueryCallableMap[$queryClass](
                    $query,
                    $validationContext,
                    $nodeQueriedFrom
                );

                if ($result !== null) {
                    // Query handled - return the result
                    return $result;
                }

                // Otherwise, query wasn't handled - keep going up the tree
                // in case a suitable ancestor context can handle the query instead
            }

            $subValidationContext = $subValidationContext->getParentContext();
        } while ($subValidationContext !== null);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function throwIfViolated()
    {
        if (count($this->violations) > 0) {
            $descriptions = [];

            foreach ($this->violations as $violation) {
                $descriptions[] = 'ACT node ' . $violation->getPath() . ' - ' . $violation->getDescription();
            }

            throw new ValidationFailureException($this, implode('. :: ', $descriptions));
        }
    }
}
