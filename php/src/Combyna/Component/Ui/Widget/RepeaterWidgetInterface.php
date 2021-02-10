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

use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;

/**
 * Interface RepeaterWidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RepeaterWidgetInterface extends CoreWidgetInterface
{
    /**
     * Fetches the widget to be repeated by this repeater
     *
     * @return WidgetInterface
     */
    public function getRepeatedWidget();

    /**
     * Evaluates the item list for the repeater and maps it to an arbitrary result array
     *
     * @param callable $mapCallback
     * @param WidgetEvaluationContextInterface $evaluationContext
     * @return array
     */
    public function mapItemStaticList(callable $mapCallback, WidgetEvaluationContextInterface $evaluationContext);

    /**
     * Sets the widget to be repeated by this repeater
     *
     * @param WidgetInterface $repeatedWidget
     */
    public function setRepeatedWidget(WidgetInterface $repeatedWidget);
}
