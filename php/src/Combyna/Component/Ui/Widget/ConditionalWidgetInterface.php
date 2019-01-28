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

use Combyna\Component\Expression\ExpressionInterface;

/**
 * Interface ConditionalWidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConditionalWidgetInterface extends CoreWidgetInterface
{
    /**
     * Fetches the widget to be shown when the condition evaluates to false, if any
     *
     * @return WidgetInterface|null
     */
    public function getAlternateWidget();

    /**
     * Fetches the expression to be evaluated to determine whether the consequent widget
     * should be shown. Must result in a boolean
     *
     * @return ExpressionInterface
     */
    public function getCondition();

    /**
     * Fetches the widget to be shown when the condition evaluates to true
     *
     * @return WidgetInterface
     */
    public function getConsequentWidget();

    /**
     * Sets the widget to be shown when the condition evaluates to false, if any.
     * If none is specified, nothing will be shown when the condition is false
     *
     * TODO: Invert these and just define a ->setParentWidget(...) method on WidgetInterface
     *
     * @param WidgetInterface|null $alternateWidget
     */
    public function setAlternateWidget(WidgetInterface $alternateWidget = null);

    /**
     * Sets the widget to be shown when the condition evaluates to true
     *
     * @param WidgetInterface $consequentWidget
     */
    public function setConsequentWidget(WidgetInterface $consequentWidget);
}
