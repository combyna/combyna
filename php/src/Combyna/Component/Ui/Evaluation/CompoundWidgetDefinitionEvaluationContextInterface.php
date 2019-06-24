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

use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface CompoundWidgetDefinitionEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CompoundWidgetDefinitionEvaluationContextInterface extends WidgetDefinitionEvaluationContextInterface
{
    /**
     * Fetches the specified child of the current compound widget
     *
     * @param string $childName
     * @return WidgetInterface
     */
    public function getChildWidget($childName);
}
