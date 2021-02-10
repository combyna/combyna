<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Ui\Widget\CoreWidgetInterface;

/**
 * Interface CoreWidgetEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CoreWidgetEvaluationContextInterface extends WidgetEvaluationContextInterface
{
    /**
     * Fetches the widget to evaluate in the context of
     *
     * @return CoreWidgetInterface
     */
    public function getWidget();
}
