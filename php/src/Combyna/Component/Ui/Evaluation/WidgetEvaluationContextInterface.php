<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface WidgetEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetEvaluationContextInterface extends ViewEvaluationContextInterface
{
    /**
     * Fetches the widget to evaluate in the context of
     *
     * @return WidgetInterface
     */
    public function getWidget();
}
