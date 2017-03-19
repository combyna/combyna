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
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;

/**
 * Interface WidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetInterface
{
    const LIBRARY = 'core';

    /**
     * Adds another widget as a child of this one
     *
     * @param string $childName
     * @param WidgetInterface $childWidget
     */
    public function addChild($childName, WidgetInterface $childWidget);

    /**
     * Checks that the provided static bag is a valid set of attributes for this widget
     *
     * @param StaticBagInterface $attributeStaticBag
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag);

    /**
     * Fetches the unique name for the definition of this widget
     *
     * @return string
     */
    public function getDefinitionLibraryName();

    /**
     * Fetches the unique name for the definition of this widget
     *
     * @return string
     */
    public function getDefinitionName();

    /**
     * Fetches the path to this widget, with its view name and all ancestor IDs
     *
     * @return string
     */
    public function getPath();

    /**
     * Renders this widget to a RenderedWidget
     *
     * @param ViewEvaluationContextInterface $evaluationContext
     * @return RenderedWidgetInterface|null Returns the rendered widget or null if invisible
     */
    public function render(ViewEvaluationContextInterface $evaluationContext);
}
