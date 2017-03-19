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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Interface ViewEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewEvaluationContextInterface extends EvaluationContextInterface
{
    /**
     * Creates a WidgetEvaluationContext
     *
     * @param WidgetInterface $widget
     * @return WidgetEvaluationContextInterface
     */
    public function createSubWidgetEvaluationContext(WidgetInterface $widget);
}
