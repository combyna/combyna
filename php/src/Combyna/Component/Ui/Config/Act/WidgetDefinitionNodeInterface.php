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
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Interface WidgetDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Determines whether this definition defines the specified value
     *
     * @param string $valueName
     * @return bool
     */
    public function definesValue($valueName);

    /**
     * Fetches the fixed static bag model for attributes of widgets with this definition
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getAttributeBagModel();

    /**
     * Fetches a child widget definition by its name, if defined for this widget definition
     *
     * @param string $childName
     * @param QueryRequirementInterface $queryRequirement
     * @return ChildWidgetDefinitionNode|null
     */
    public function getChildDefinition($childName, QueryRequirementInterface $queryRequirement);

    /**
     * Fetches all event definition references defined for this widget definition
     *
     * @return EventDefinitionReferenceNode[]
     */
    public function getEventDefinitionReferences();

    /**
     * Fetches the name of the library this definition belongs to
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the type of a value defined by this definition
     *
     * @param string $valueName
     * @param QueryRequirementInterface $queryRequirement
     * @return TypeInterface
     */
    public function getValueType($valueName, QueryRequirementInterface $queryRequirement);

    /**
     * Fetches the name of this widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionName();

    /**
     * Returns whether or not this widget definition is defined
     *
     * @return bool
     */
    public function isDefined();

    /**
     * Validates that the provided widget data will produce a valid widget with this definition
     *
     * @param ValidationContextInterface $validationContext
     * @param ExpressionBagNode $attributeExpressionBagNode
     * @param WidgetNodeInterface[] $childWidgetNodes
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    );
}
