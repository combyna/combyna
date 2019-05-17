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
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;

/**
 * Interface WidgetNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the bag of expressions to assign to captures defined by ancestors
     *
     * @return ExpressionBagNode
     */
    public function getCaptureExpressionBag();

    /**
     * Fetches the model for values to capture within this widget's context
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getCaptureStaticBagModel();

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
