<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param string $text
     * @return TextWidgetStateInterface
     */
    public function with($text);
}
