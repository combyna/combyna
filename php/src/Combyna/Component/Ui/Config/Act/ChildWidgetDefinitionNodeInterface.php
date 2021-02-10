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

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface ChildWidgetDefinitionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ChildWidgetDefinitionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the name of the widget child
     *
     * @return string
     */
    public function getChildName();

    /**
     * Returns whether or not this child definition is defined
     *
     * @return bool
     */
    public function isDefined();

    /**
     * Returns whether this child must be specified for each widget instance
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Validates that the provided widget is a valid child for this definition
     *
     * @param WidgetNodeInterface $widgetNode
     * @param ValidationContextInterface $validationContext
     */
    public function validateWidget(WidgetNodeInterface $widgetNode, ValidationContextInterface $validationContext);
}
