<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface WidgetDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the fixed static bag model for attributes of widgets with this definition
     *
     * @return FixedStaticBagModelNode
     */
    public function getAttributeBagModel();

    /**
     * Fetches the name of the library this definition belongs to
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the name of this widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionName();

    /**
     * Validates that the provided widget data will produce a valid widget with this definition
     *
     * @param ValidationContextInterface $validationContext
     * @param ExpressionBagNode $attributeExpressionBagNode
     * @param WidgetNode[] $childWidgetNodes
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    );
}
