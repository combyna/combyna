<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;

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
     * Creates an event to dispatch for a rendered instance of a widget of this definition
     *
     * @param string $libraryName
     * @param string $eventName
     * @param StaticBagInterface $payloadStaticBag
     * @return EventInterface
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag);

    /**
     * Creates a DefinedWidgetState
     *
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @return DefinedWidgetStateInterface
     */
    public function createInitialState(
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag
    );

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

    /**
     * Returns true if this widget definition has the specified label, false otherwise
     *
     * @param string $label
     * @return bool
     */
    public function hasLabel($label);

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
