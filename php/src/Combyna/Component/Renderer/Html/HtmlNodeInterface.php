<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
     * Returns an array representation of this node
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns a HTML representation of this node
     *
     * @return string
     */
    public function toHtml();

    /**
     * Returns an array representation of this node's root-level element(s).
     * If this node only contains one child then it should still be wrapped in a one-element array,
     * eg. `[['type' => 'some-type']]`
     *
     * @return array
     */
    public function toGenericArray();
}
