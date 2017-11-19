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
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;

/**
 * Interface WidgetNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the children of this widget, if any have been added
     *
     * @return WidgetNodeInterface[]
     */
    public function getChildWidgets();

    /**
     * Fetches the name of the library this widget's definition should be fetched from
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the tags for this widget
     *
     * @return array
     */
    public function getTags();

    /**
     * Fetches the expression used to determine whether this widget is visible, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getVisibilityExpression();

    /**
     * Fetches the name of the definition for this widget
     *
     * @return string
     */
    public function getWidgetDefinitionName();
}
