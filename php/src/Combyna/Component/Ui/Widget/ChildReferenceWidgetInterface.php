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

/**
 * Interface ChildReferenceWidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ChildReferenceWidgetInterface extends CoreWidgetInterface
{
    /**
     * Fetches the name of the referenced child widget of the compound widget
     *
     * @return string
     */
    public function getChildName();
}
