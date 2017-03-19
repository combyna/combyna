<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

/**
 * Interface HtmlNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface HtmlNodeInterface
{
    /**
     * Returns a HTML representation of this node
     *
     * @return string
     */
    public function toHtml();
}
