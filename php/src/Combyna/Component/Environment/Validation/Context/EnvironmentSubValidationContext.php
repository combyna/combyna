<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\FunctionNodeInterface;
use Combyna\Component\Event\Validation\Query\EventDefinitionExistsQuery;
use Combyna\Component\Event\Validation\Query\EventDefinitionPayloadStaticTypeQuery;
use Combyna\Component\Expression\Validation\Query\FunctionNodeQuery;
use Combyna\Component\Expression\Validation\Query\FunctionReturnTypeQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionExistsQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionHasPayloadStaticQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionPayloadStaticTypeQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionHasValueQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionNodeQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionValueTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class EnvironmentSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentSubValidationContext implements EnvironmentSubValidationContextInterface
{
    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $environmentNodeBehaviourSpec;

    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var ActNodeInterface
     */
    private $subjectNode;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param EnvironmentNode $environmentNode
     * @param BehaviourSpecInterface $environmentNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        EnvironmentNode $environmentNode,
        BehaviourSpecInterface $environmentNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->environmentNode = $environmentNode;
        $this->environmentNodeBehaviourSpec = $environmentNodeBehaviourSpec;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->environmentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->environmentNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        return $this->parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '[environment]';
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            EventDefinitionExistsQuery::class => [$this, 'queryForEventDefinitionExistence'],
            EventDefinitionPayloadStaticTypeQuery::class => [$this, 'queryForEventPayloadStaticType'],
            FunctionNodeQuery::class => [$this, 'queryForFunctionActNode'],
            FunctionReturnTypeQuery::class => [$this, 'queryForFunctionReturnType'],
            SignalDefinitionExistsQuery::class => [$this, 'queryForSignalDefinitionExistence'],
            SignalDefinitionPayloadStaticTypeQuery::class => [$this, 'queryForSignalPayloadStaticType'],
            WidgetDefinitionHasValueQuery::class => [$this, 'queryForWidgetValueExistence'],
            WidgetDefinitionNodeQuery::class => [$this, 'queryForWidgetDefinitionNode'],
            WidgetDefinitionValueTypeQuery::class => [$this, 'queryForWidgetValueType']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActNode()
    {
        return $this->subjectNode;
    }

    /**
     * Determines whether the specified event definition exists
     *
     * @param EventDefinitionExistsQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool|null
     */
    public function queryForEventDefinitionExistence(
        EventDefinitionExistsQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $eventDefinitionNode = $this->environmentNode->getEventDefinition(
            $query->getLibraryName(),
            $query->getEventName(),
            $validationContext->createBooleanQueryRequirement($query)
        );

        if ($eventDefinitionNode->isDefined()) {
            // We've discovered that the library _does_ define the requested event definition
            return true;
        }

        // The library doesn't define the requested event definition - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Fetches the type of the specified payload static of an event defined by a library
     *
     * @param EventDefinitionPayloadStaticTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForEventPayloadStaticType(
        EventDefinitionPayloadStaticTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $eventDefinitionNode = $this->environmentNode->getEventDefinition(
            $query->getEventLibraryName(),
            $query->getEventName(),
            $validationContext->createTypeQueryRequirement($query)
        );

        return $eventDefinitionNode->getPayloadStaticType($query->getPayloadStaticName());
    }

    /**
     * Fetches the ACT node of the specified function defined by a library
     *
     * @param FunctionNodeQuery $query
     * @param ValidationContextInterface $validationContext
     * @param ActNodeInterface $nodeQueriedFrom
     * @return FunctionNodeInterface
     */
    public function queryForFunctionActNode(
        FunctionNodeQuery $query,
        ValidationContextInterface $validationContext,
        ActNodeInterface $nodeQueriedFrom
    ) {
        return $this->environmentNode->getGenericFunction(
            $query->getLibraryName(),
            $query->getFunctionName(),
            $validationContext->createActNodeQueryRequirement($query, $nodeQueriedFrom)
        );
    }

    /**
     * Fetches the return type of the specified function defined by a library
     *
     * @param FunctionReturnTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForFunctionReturnType(
        FunctionReturnTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $this->environmentNode
            ->getGenericFunction(
                $query->getLibraryName(),
                $query->getFunctionName(),
                $validationContext->createTypeQueryRequirement($query)
            )
            ->getReturnTypeDeterminer()
            ->determine($validationContext);
    }

    /**
     * Determines whether the specified signal definition exists
     *
     * @param SignalDefinitionExistsQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool|null
     */
    public function queryForSignalDefinitionExistence(
        SignalDefinitionExistsQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $signalDefinitionNode = $this->environmentNode->getSignalDefinition(
            $query->getLibraryName(),
            $query->getSignalName(),
            $validationContext->createBooleanQueryRequirement($query)
        );

        if ($signalDefinitionNode->isDefined()) {
            // We've discovered that the library _does_ define the requested signal definition
            return true;
        }

        // The library doesn't define the requested signal definition - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Determines whether the specified signal defines the specified payload,
     * where the signal is defined by a library
     *
     * @param SignalDefinitionHasPayloadStaticQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForSignalPayloadStaticExistence(
        SignalDefinitionHasPayloadStaticQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createBooleanQueryRequirement($query);

        $signalDefinitionNode = $this->environmentNode->getSignalDefinition(
            $query->getSignalLibraryName(),
            $query->getSignalName(),
            $queryRequirement
        );

        return $signalDefinitionNode->getPayloadStaticBagModel()->definesStatic($query->getPayloadStaticName());
    }

    /**
     * Fetches the return type of the specified payload static of a signal defined by a library
     *
     * @param SignalDefinitionPayloadStaticTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForSignalPayloadStaticType(
        SignalDefinitionPayloadStaticTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        $signalDefinitionNode = $this->environmentNode->getSignalDefinition(
            $query->getSignalLibraryName(),
            $query->getSignalName(),
            $queryRequirement
        );

        return $signalDefinitionNode->getPayloadStaticType($query->getPayloadStaticName(), $queryRequirement);
    }

    /**
     * Fetches a WidgetDefinitionNode defined by a library
     *
     * @param WidgetDefinitionNodeQuery $query
     * @param ValidationContextInterface $validationContext
     * @param ActNodeInterface $nodeQueriedFrom
     * @return WidgetDefinitionNodeInterface
     */
    public function queryForWidgetDefinitionNode(
        WidgetDefinitionNodeQuery $query,
        ValidationContextInterface $validationContext,
        ActNodeInterface $nodeQueriedFrom
    ) {
        $widgetDefinitionNode = $this->environmentNode->getWidgetDefinition(
            $query->getLibraryName(),
            $query->getWidgetDefinitionName(),
            $validationContext->createActNodeQueryRequirement($query, $nodeQueriedFrom)
        );

        return $widgetDefinitionNode;
    }

    /**
     * Determines whether the specified widget definition defines the specified value
     *
     * @param WidgetDefinitionHasValueQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForWidgetValueExistence(
        WidgetDefinitionHasValueQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $widgetDefinitionNode = $this->environmentNode->getWidgetDefinition(
            $query->getLibraryName(),
            $query->getWidgetDefinitionName(),
            $validationContext->createBooleanQueryRequirement($query)
        );

        return $widgetDefinitionNode->definesValue($query->getValueName());
    }

    /**
     * Fetches the return type of the specified value of a widget defined by a library
     *
     * @param WidgetDefinitionValueTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForWidgetValueType(
        WidgetDefinitionValueTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        $widgetDefinitionNode = $this->environmentNode->getWidgetDefinition(
            $query->getLibraryName(),
            $query->getWidgetDefinitionName(),
            $queryRequirement
        );

        return $widgetDefinitionNode->getValueType($query->getValueName(), $queryRequirement);
    }
}
