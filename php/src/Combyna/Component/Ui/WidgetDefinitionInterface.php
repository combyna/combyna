<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface WidgetDefinitionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionInterface
{
    /**
     * Checks that the provided static bag is a valid set of attributes for a widget of this definition
     *
     * @param StaticBagInterface $attributeStaticBag
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag);

    /**
     * Fetches the name of the library this widget definition is defined by
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name for this type of widget
     *
     * @return string
     */
    public function getName();

//    /**
//     * Ensures that the provided attribute bag is valid
//     *
//     * @param ValidationContextInterface $validationContext
//     * @param ExpressionBagNode $expressionBagNode
//     */
//    public function validateAttributeExpressions(
//        ValidationContextInterface $validationContext,
//        ExpressionBagNode $expressionBagNode
//    );
}
