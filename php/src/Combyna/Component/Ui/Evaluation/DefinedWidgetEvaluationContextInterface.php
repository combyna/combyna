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

use Combyna\Component\Ui\Widget\DefinedWidgetInterface;

/**
 * Interface DefinedWidgetEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DefinedWidgetEvaluationContextInterface extends WidgetEvaluationContextInterface
{
    /**
     * Fetches the widget to evaluate in the context of
     *
     * @return DefinedWidgetInterface
     */
    public function getWidget();
}
