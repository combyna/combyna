<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Ui\Validation\Constraint\ValidWidgetValueProvidersConstraint;
use Combyna\Component\Ui\Validation\Context\Specifier\PrimitiveWidgetDefinitionContextSpecifier;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class PrimitiveWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetDefinitionNode extends AbstractActNode implements WidgetDefinitionNodeInterface
{
    const TYPE = PrimitiveWidgetDefinition::TYPE;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $attributeBagModelNode;

    /**
     * @var ChildWidgetDefinitionNodeInterface[]
     */
    private $childDefinitionNodes;

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var EventDefinitionReferenceNode[]
     */
    private $eventDefinitionReferenceNodes;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $valueBagModelNode;

    /**
     * @var callable[]
     */
    private $valueNameToProviderCallableMap = [];

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param FixedStaticBagModelNodeInterface $attributeBagModelNode
     * @param FixedStaticBagModelNodeInterface $valueBagModelNode
     * @param ChildWidgetDefinitionNodeInterface[] $childDefinitionNodes
     * @param EventDefinitionReferenceNode[] $eventDefinitionReferenceNodes
     */
    public function __construct(
        $libraryName,
        $widgetDefinitionName,
        FixedStaticBagModelNodeInterface $attributeBagModelNode,
        FixedStaticBagModelNodeInterface $valueBagModelNode,
        array $childDefinitionNodes,
        array $eventDefinitionReferenceNodes
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->childDefinitionNodes = $childDefinitionNodes;
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->eventDefinitionReferenceNodes = $eventDefinitionReferenceNodes;
        $this->libraryName = $libraryName;
        $this->name = $widgetDefinitionName;
        $this->valueBagModelNode = $valueBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeBagModelNode);
        $specBuilder->addChildNode($this->dynamicContainerNode);

        foreach ($this->childDefinitionNodes as $childDefinitionNode) {
            $specBuilder->addChildNode($childDefinitionNode);
        }

        foreach ($this->eventDefinitionReferenceNodes as $eventDefinitionReferenceNode) {
            $specBuilder->addChildNode($eventDefinitionReferenceNode);
        }

        $specBuilder->addSubSpec(function (BehaviourSpecBuilderInterface $subSpecBuilder) {
            // Only give the widget value defaults the primitive widget definition context,
            // as primitive widget attrs cannot reference other primitive widget attrs
            // for the same widget
            $subSpecBuilder->defineValidationContext(
                new PrimitiveWidgetDefinitionContextSpecifier()
            );

            $subSpecBuilder->addChildNode($this->valueBagModelNode);
            $subSpecBuilder->addConstraint(
                new ValidWidgetValueProvidersConstraint(
                    $this->libraryName,
                    $this->name,
                    $this->valueBagModelNode->getStaticDefinitionNames(),
                    function () {
                        // Defer the fetching of the providers with a callback,
                        // to allow providers to be installed before validation
                        return $this->valueNameToProviderCallableMap;
                    }
                )
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function definesValue($valueName)
    {
        return $this->valueBagModelNode->definesStatic($valueName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBagModel()
    {
        return $this->attributeBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildDefinition($childName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        return array_key_exists($childName, $this->childDefinitionNodes) ?
            $this->childDefinitionNodes[$childName] :
            new UnknownChildWidgetDefinitionNode($childName, $dynamicActNodeAdopter);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitionReferences()
    {
        return $this->eventDefinitionReferenceNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'primitive-widget-def:' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the fixed static bag model for values of widgets with this definition
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getValueBagModel()
    {
        return $this->valueBagModelNode;
    }

    /**
     * Fetches the map of widget value names to provider callables
     *
     * @return callable[]
     */
    public function getValueNameToProviderCallableMap()
    {
        return $this->valueNameToProviderCallableMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueType($valueName)
    {
        return $this->dynamicContainerNode->determineType(
            $this->valueBagModelNode
                ->getStaticDefinitionByName($valueName, $this->dynamicContainerNode)
                ->getStaticTypeDeterminer()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return true;
    }

    /**
     * Sets the provider to use for a widget value. If any of the widget values
     * do not get a provider installed, this definition will fail validation
     *
     * @param string $valueName
     * @param callable $callable
     */
    public function setValueProviderCallable($valueName, callable $callable)
    {
        $this->valueNameToProviderCallableMap[$valueName] = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        $this->attributeBagModelNode->validateStaticExpressionBag(
            $validationContext,
            $attributeExpressionBagNode,
            'attributes for primitive "' . $this->name . '" widget of library "' . $this->libraryName . '"'
        );

        // Check there are no required children that are missing an expression
        foreach ($this->childDefinitionNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $childWidgetNodes) && $definitionNode->isRequired()) {
                $validationContext->addGenericViolation(sprintf(
                    'Primitive widget is missing required child "%s"',
                    $childName
                ));
            }
        }

        // Check there are no child widgets that aren't needed/are extra
        foreach ($childWidgetNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $this->childDefinitionNodes)) {
                $validationContext->addGenericViolation(sprintf(
                    'Primitive widget has an unnecessary extra child widget "%s"',
                    $childName
                ));
            }
        }

        // Check all child widgets provided can only ever evaluate to valid primitives
        // for their corresponding children
        foreach ($this->childDefinitionNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $childWidgetNodes)) {
                // Skip any undefined children as we won't be able to fetch them.
                // Validation should already have been marked failed above
                continue;
            }

            $childWidgetNode = $childWidgetNodes[$childName];

            $definitionNode->validateWidget($childWidgetNode, $validationContext);
        }
    }
}
