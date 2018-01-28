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
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
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
     * @var FixedStaticBagModelNode
     */
    private $attributeBagModelNode;

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
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param FixedStaticBagModelNode $attributeBagModelNode
     * @param EventDefinitionReferenceNode[] $eventDefinitionReferenceNodes
     */
    public function __construct(
        $libraryName,
        $widgetDefinitionName,
        FixedStaticBagModelNode $attributeBagModelNode,
        array $eventDefinitionReferenceNodes
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->eventDefinitionReferenceNodes = $eventDefinitionReferenceNodes;
        $this->libraryName = $libraryName;
        $this->name = $widgetDefinitionName;
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
    public function getEventDefinitionReferences()
    {
        return $this->eventDefinitionReferenceNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
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
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeBagModelNode->validate($subValidationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeBagModelNode->validateStaticExpressionBag(
            $subValidationContext,
            $attributeExpressionBagNode,
            'attributes for core "' . $this->name . '" widget of library "' . $this->libraryName . '"'
        );

        // TODO: Check that all EventDefinitionReferences are valid (ie. reference valid definitions)
    }
}
