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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Event\Config\Act\EventDefinitionNodeInterface;
use Combyna\Component\Event\Config\Act\UnknownEventDefinitionNode;
use Combyna\Component\Signal\Config\Act\DynamicUnknownSignalDefinitionNode;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNodeInterface;
use Combyna\Component\Ui\Config\Act\PrimitiveWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\UnknownWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;
use LogicException;

/**
 * Class LibraryNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryNode extends AbstractActNode
{
    const TYPE = 'library';

    /**
     * @var string
     */
    private $description;

    /**
     * @var EventDefinitionNodeInterface[]
     */
    private $eventDefinitionNodes = [];

    /**
     * @var FunctionNodeInterface[]
     */
    private $functionNodes = [];

    /**
     * @var string[]
     */
    private $libraryNamesDependedOn;

    /**
     * @var string
     */
    private $name;

    /**
     * @var SignalDefinitionNodeInterface[]
     */
    private $signalDefinitionNodes = [];

    /**
     * @var WidgetDefinitionNodeInterface[]
     */
    private $widgetDefinitionNodes = [];

    /**
     * @param string $name
     * @param string $description
     * @param string[] $libraryNamesDependedOn
     * @param FunctionNodeInterface[] $functionNodes
     * @param EventDefinitionNodeInterface[] $eventDefinitionNodes
     * @param SignalDefinitionNodeInterface[] $signalDefinitionNodes
     * @param WidgetDefinitionNodeInterface[] $widgetDefinitionNodes
     */
    public function __construct(
        $name,
        $description,
        array $libraryNamesDependedOn = [],
        array $functionNodes = [],
        array $eventDefinitionNodes = [],
        array $signalDefinitionNodes = [],
        array $widgetDefinitionNodes = []
    ) {
        $this->description = $description;

        // Index functions by name to simplify lookups
        foreach ($functionNodes as $functionNode) {
            $this->functionNodes[$functionNode->getName()] = $functionNode;
        }

        $this->libraryNamesDependedOn = $libraryNamesDependedOn;
        $this->name = $name;

        // Index event definitions by name to simplify lookups
        foreach ($eventDefinitionNodes as $eventDefinitionNode) {
            $this->eventDefinitionNodes[$eventDefinitionNode->getEventName()] = $eventDefinitionNode;
        }

        // Index signal definitions by name to simplify lookups
        foreach ($signalDefinitionNodes as $signalDefinitionNode) {
            $this->signalDefinitionNodes[$signalDefinitionNode->getSignalName()] = $signalDefinitionNode;
        }

        // Index widget definitions by name to simplify lookups
        foreach ($widgetDefinitionNodes as $widgetDefinitionNode) {
            $this->widgetDefinitionNodes[$widgetDefinitionNode->getWidgetDefinitionName()] = $widgetDefinitionNode;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->eventDefinitionNodes as $eventDefinitionNode) {
            $specBuilder->addChildNode($eventDefinitionNode);
        }

        foreach ($this->functionNodes as $functionNode) {
            $specBuilder->addChildNode($functionNode);
        }

        foreach ($this->signalDefinitionNodes as $signalDefinitionNode) {
            $specBuilder->addChildNode($signalDefinitionNode);
        }

        foreach ($this->widgetDefinitionNodes as $widgetDefinitionNode) {
            $specBuilder->addChildNode($widgetDefinitionNode);
        }
    }

    /**
     * Fetches the human-readable description of this library
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Fetches an event definition defined by this library
     *
     * @param string $eventName
     * @param QueryRequirementInterface $queryRequirement
     * @return EventDefinitionNodeInterface
     */
    public function getEventDefinition($eventName, QueryRequirementInterface $queryRequirement)
    {
        if (!array_key_exists($eventName, $this->eventDefinitionNodes)) {
            return new UnknownEventDefinitionNode($this->name, $eventName, $queryRequirement);
        }

        return $this->eventDefinitionNodes[$eventName];
    }

    /**
     * Fetches all event definitions defined by this library
     *
     * @return EventDefinitionNodeInterface[]
     */
    public function getEventDefinitions()
    {
        return $this->eventDefinitionNodes;
    }

    /**
     * Fetches all functions defined by this library
     *
     * @return FunctionNodeInterface[]
     */
    public function getFunctions()
    {
        return $this->functionNodes;
    }

    /**
     * Fetches a function defined by this library. If the library
     * does not define the specified function, then a DynamicUnknownFunctionNode will be returned.
     * If the library does define the function but not of a generic type,
     * then an IncorrectTypeFunctionNode will be returned
     *
     * @param string $functionName
     * @param QueryRequirementInterface $queryRequirement
     * @return FunctionNodeInterface
     */
    public function getGenericFunction($functionName, QueryRequirementInterface $queryRequirement)
    {
        if (!array_key_exists($functionName, $this->functionNodes)) {
            return new DynamicUnknownFunctionNode($this->name, $functionName, $queryRequirement);
        }

        // TODO: Check type of function and return IncorrectTypeFunctionNode if wrong

        return $this->functionNodes[$functionName];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->name;
    }

    /**
     * Fetches the unique name of this library
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches a signal definition defined by this library
     *
     * @param string $signalName
     * @param QueryRequirementInterface $queryRequirement
     * @return SignalDefinitionNodeInterface
     */
    public function getSignalDefinition($signalName, QueryRequirementInterface $queryRequirement)
    {
        if (!array_key_exists($signalName, $this->signalDefinitionNodes)) {
            return new DynamicUnknownSignalDefinitionNode($this->name, $signalName, $queryRequirement);
        }

        return $this->signalDefinitionNodes[$signalName];
    }

    /**
     * Fetches all signal definitions defined by this library
     *
     * @return SignalDefinitionNodeInterface[]
     */
    public function getSignalDefinitions()
    {
        return $this->signalDefinitionNodes;
    }

    /**
     * Fetches a widget definition defined by this library.
     * If the library does not define the specified definition,
     * then an UnknownWidgetDefinitionNode will be returned
     *
     * @param string $name
     * @param QueryRequirementInterface $queryRequirement
     * @return WidgetDefinitionNodeInterface
     */
    public function getWidgetDefinition($name, QueryRequirementInterface $queryRequirement)
    {
        if (!array_key_exists($name, $this->widgetDefinitionNodes)) {
            return new UnknownWidgetDefinitionNode($this->name, $name, $queryRequirement);
        }

        return $this->widgetDefinitionNodes[$name];
    }

    /**
     * Fetches all widget definitions defined by this library
     *
     * @return WidgetDefinitionNodeInterface[]
     */
    public function getWidgetDefinitions()
    {
        return $this->widgetDefinitionNodes;
    }

    /**
     * Installs a native function, referenced by a NativeFunctionNode
     *
     * @param string $functionName
     * @param callable $callable
     * @throws FunctionNotSupportedException
     * @throws LogicException
     */
    public function installNativeFunction($functionName, callable $callable)
    {
        if (!array_key_exists($functionName, $this->functionNodes)) {
            throw new FunctionNotSupportedException($this->name, $functionName);
        }

        $functionNode = $this->functionNodes[$functionName];

        if (!$functionNode instanceof NativeFunctionNode) {
            throw new LogicException('Only native function nodes can reference a native function');
        }

        $functionNode->setNativeFunctionCallable($callable);
    }

    /**
     * Installs a widget value provider
     *
     * @param string $widgetDefinitionName
     * @param string $valueName
     * @param callable $callable
     * @throws LogicException
     * @throws WidgetDefinitionNotSupportedException
     */
    public function installWidgetValueProvider($widgetDefinitionName, $valueName, callable $callable)
    {
        if (!array_key_exists($widgetDefinitionName, $this->widgetDefinitionNodes)) {
            throw new WidgetDefinitionNotSupportedException($this->name, $widgetDefinitionName);
        }

        $widgetDefinitionNode = $this->widgetDefinitionNodes[$widgetDefinitionName];

        if (!$widgetDefinitionNode instanceof PrimitiveWidgetDefinitionNode) {
            throw new LogicException(
                'Only primitive widget definition nodes can define values with providers'
            );
        }

        $widgetDefinitionNode->setValueProviderCallable($valueName, $callable);
    }

    /**
     * Determines whether anything in this library references the specified other one
     *
     * @param string $otherLibraryName
     * @return bool
     */
    public function referencesLibrary($otherLibraryName)
    {
        return in_array($otherLibraryName, $this->libraryNamesDependedOn, true);
    }
}
