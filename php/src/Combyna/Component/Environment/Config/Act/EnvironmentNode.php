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
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Environment\Validation\Context\Specifier\EnvironmentContextSpecifier;
use Combyna\Component\Event\Config\Act\EventDefinitionNodeInterface;
use Combyna\Component\Event\Config\Act\UnknownLibraryForEventDefinitionNode;
use Combyna\Component\Framework\Config\Act\RootNodeInterface;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNodeInterface;
use Combyna\Component\Signal\Config\Act\UnknownLibraryForSignalDefinitionNode;
use Combyna\Component\Ui\Config\Act\UnknownLibraryForWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class EnvironmentNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentNode extends AbstractActNode implements RootNodeInterface
{
    const TYPE = 'environment';

    /**
     * @var LibraryNode[]
     */
    private $libraryNodes = [];

    /**
     * @param LibraryNode[] $libraryNodes
     */
    public function __construct(array $libraryNodes = [])
    {
        // Index the libraries by name to simplify lookups
        foreach ($libraryNodes as $libraryNode) {
            $this->libraryNodes[$libraryNode->getName()] = $libraryNode;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->defineValidationContext(new EnvironmentContextSpecifier());

        foreach ($this->libraryNodes as $libraryNode) {
            $specBuilder->addChildNode($libraryNode);
        }
    }

    /**
     * Fetches an event definition defined by a library installed into the environment.
     * If the library is not installed then an UnknownLibraryForEventDefinitionNode,
     * or if it is but does not define the specified definition,
     * then an UnknownEventDefinitionNode will be returned
     *
     * @param string $libraryName
     * @param string $eventDefinitionName
     * @param QueryRequirementInterface $queryRequirement
     * @return EventDefinitionNodeInterface
     */
    public function getEventDefinition(
        $libraryName,
        $eventDefinitionName,
        QueryRequirementInterface $queryRequirement
    ) {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            // Not even the library was found
            return new UnknownLibraryForEventDefinitionNode($libraryName, $eventDefinitionName, $queryRequirement);
        }

        return $this->libraryNodes[$libraryName]->getEventDefinition($eventDefinitionName, $queryRequirement);
    }

    /**
     * Fetches a function defined by a library installed into the environment.
     * If the library is not installed then an UnknownLibraryForFunctionNode,
     * or if it is but does not define the specified function,
     * then a DynamicUnknownFunctionNode will be returned
     *
     * @param string $libraryName
     * @param string $functionName
     * @param QueryRequirementInterface $queryRequirement
     * @return FunctionNodeInterface
     */
    public function getGenericFunction($libraryName, $functionName, QueryRequirementInterface $queryRequirement)
    {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            // Not even the library was found
            return new UnknownLibraryForFunctionNode($libraryName, $functionName, $queryRequirement);
        }

        return $this->libraryNodes[$libraryName]->getGenericFunction($functionName, $queryRequirement);
    }

    /**
     * Fetches all libraries installed into this environment
     *
     * @return LibraryNode[]
     */
    public function getLibraries()
    {
        return $this->libraryNodes;
    }

    /**
     * Fetches a signal definition defined by a library installed into the environment.
     * If the library is not installed then an UnknownLibraryForSignalDefinitionNode,
     * or if it is but does not define the specified definition,
     * then an UnknownSignalDefinitionNode will be returned
     *
     * @param string $libraryName
     * @param string $signalDefinitionName
     * @param QueryRequirementInterface $queryRequirement
     * @return SignalDefinitionNodeInterface
     */
    public function getSignalDefinition(
        $libraryName,
        $signalDefinitionName,
        QueryRequirementInterface $queryRequirement
    ) {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            // Not even the library was found
            return new UnknownLibraryForSignalDefinitionNode($libraryName, $signalDefinitionName, $queryRequirement);
        }

        return $this->libraryNodes[$libraryName]->getSignalDefinition($signalDefinitionName, $queryRequirement);
    }

    /**
     * Fetches a widget definition defined by a library installed into the environment.
     * If the library is not installed then an UnknownLibraryForWidgetDefinitionNode will be returned,
     * or if it is but does not define the specified definition,
     * then an UnknownWidgetDefinitionNode will be returned
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param QueryRequirementInterface $queryRequirement
     * @return WidgetDefinitionNodeInterface
     */
    public function getWidgetDefinition(
        $libraryName,
        $widgetDefinitionName,
        QueryRequirementInterface $queryRequirement
    ) {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            // Not even the library was found
            return new UnknownLibraryForWidgetDefinitionNode($libraryName, $widgetDefinitionName, $queryRequirement);
        }

        return $this->libraryNodes[$libraryName]->getWidgetDefinition($widgetDefinitionName, $queryRequirement);
    }

    /**
     * Installs a new library into the environment
     *
     * @param LibraryNode $libraryNode
     */
    public function installLibrary(LibraryNode $libraryNode)
    {
        $this->libraryNodes[$libraryNode->getName()] = $libraryNode;
    }

    /**
     * Installs a native function referenced by a NativeFunctionNode
     *
     * @param string $libraryName
     * @param string $functionName
     * @param callable $callable
     * @throws LibraryNotInstalledException
     * @throws FunctionNotSupportedException
     */
    public function installNativeFunction($libraryName, $functionName, callable $callable)
    {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            throw new LibraryNotInstalledException($libraryName);
        }

        return $this->libraryNodes[$libraryName]->installNativeFunction(
            $functionName,
            $callable
        );
    }

    /**
     * Installs a widget value provider
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string $valueName
     * @param callable $callable
     * @throws LibraryNotInstalledException
     * @throws WidgetDefinitionNotSupportedException
     */
    public function installWidgetValueProvider(
        $libraryName,
        $widgetDefinitionName,
        $valueName,
        callable $callable
    ) {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            throw new LibraryNotInstalledException($libraryName);
        }

        return $this->libraryNodes[$libraryName]->installWidgetValueProvider(
            $widgetDefinitionName,
            $valueName,
            $callable
        );
    }
}
