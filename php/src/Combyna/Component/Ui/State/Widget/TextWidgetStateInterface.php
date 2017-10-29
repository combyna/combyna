<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

/**
 * Interface TextWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TextWidgetStateInterface extends CoreWidgetStateInterface
{
    const TYPE = 'text-widget';

    /**
     * Fetches the text content to display for this widget
     *
     * @return string
     */
    public function getText();
}
