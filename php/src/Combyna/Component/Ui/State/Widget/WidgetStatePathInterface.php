<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

use Combyna\Component\State\StatePathInterface;

/**
 * Interface WidgetStatePathInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetStatePathInterface extends StatePathInterface
{
    const WIDGET_DEFINITION_PATH_TYPE = 'widget';
    const VIEW_PATH_TYPE = 'view';

    /**
     * Creates a WidgetStatePath to the specified child state of the end state in this path
     *
     * @param string $childName
     * @return WidgetStatePathInterface
     */
    public function getChildStatePath($childName);

    /**
     * Fetches the path to the final state in the path if it is of a renderable widget,
     * otherwise if it is a compound widget, fetches the path to the state
     * of the eventual renderable widget that it resolves to
     *
     * @return WidgetStatePathInterface
     */
    public function getEventualEndRenderableStatePath();

    /**
     * Fetches a list of state paths starting with just the root node, then the root+node2, etc.
     *
     * @return WidgetStatePathInterface[]
     */
    public function getSubStatePaths();

    /**
     * Fetches the name of the library this widget's definition belongs to
     *
     * @return string
     */
    public function getWidgetDefinitionLibraryName();

    /**
     * Fetches the unique name of the definition for this widget
     *
     * @return string
     */
    public function getWidgetDefinitionName();

    /**
     * Fetches the name-based path to the widget
     *
     * @return string[]
     */
    public function getWidgetPath();

    /**
     * Fetches the name-based path to the widget state
     * (may return the same as ->getWidgetPath(), unless a parent widget
     * is a Repeater, for example)
     *
     * @return string[]
     */
    public function getWidgetStatePath();
}
