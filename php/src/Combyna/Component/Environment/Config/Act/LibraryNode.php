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

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Library\NativeFunction;
use Combyna\Component\Event\Config\Act\EventDefinitionNode;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNode;
use Combyna\Component\Ui\Config\Act\UnknownWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
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
     * @var EventDefinitionNode[]
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
     * @var SignalDefinitionNode[]
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
     * @param EventDefinitionNode[] $eventDefinitionNodes
     * @param SignalDefinitionNode[] $signalDefinitionNodes
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
     * Fetches the human-readable description of this library
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Fetches all event definitions defined by this library
     *
     * @return EventDefinitionNode[]
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
     * does not define the specified function, then an UnknownFunctionNode will be returned.
     * If the library does define the function but not of a generic type,
     * then an IncorrectTypeFunctionNode will be returned
     *
     * @param string $functionName
     * @return FunctionNodeInterface
     */
    public function getGenericFunction($functionName)
    {
        if (!array_key_exists($functionName, $this->functionNodes)) {
            return new UnknownFunctionNode($this->name, $functionName);
        }

        // TODO: Check type of function and return IncorrectTypeFunctionNode if wrong

        return $this->functionNodes[$functionName];
    }

    /**
     * Fetches all signal definitions defined by this library
     *
     * @return SignalDefinitionNode[]
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
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionNodeInterface
     */
    public function getWidgetDefinition($widgetDefinitionName)
    {
        if (!array_key_exists($widgetDefinitionName, $this->widgetDefinitionNodes)) {
            return new UnknownWidgetDefinitionNode($this->name, $widgetDefinitionName);
        }

        return $this->widgetDefinitionNodes[$widgetDefinitionName];
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
     * Fetches the unique name of this library
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Installs a native function, referenced by a NativeFunctionNode
     *
     * @param NativeFunction $nativeFunction
     * @throws FunctionNotSupportedException
     * @throws LogicException
     */
    public function installNativeFunction(NativeFunction $nativeFunction)
    {
        $functionName = $nativeFunction->getName();

        if (!array_key_exists($functionName, $this->functionNodes)) {
            throw new FunctionNotSupportedException($this->name, $functionName);
        }

        $functionNode = $this->functionNodes[$functionName];

        if (!$functionNode instanceof NativeFunctionNode) {
            throw new LogicException('Only native function nodes can reference a native function');
        }

        $functionNode->setNativeFunction($nativeFunction);
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

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->functionNodes as $functionNode) {
            $functionNode->validate($subValidationContext);
        }

        foreach ($this->widgetDefinitionNodes as $widgetDefinitionNode) {
            $widgetDefinitionNode->validate($subValidationContext);
        }
    }
}
