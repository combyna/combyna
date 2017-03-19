<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Interface RenderedViewInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RenderedViewInterface
{
    /**
     * Fetches the specified attribute, evaluated to a static for this rendered view
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the bag does not contain the specified static
     */
    public function getAttribute($name);

    /**
     * Fetches the rendered root widget for this view
     *
     * @return RenderedWidgetInterface
     */
    public function getRootWidget();

    /**
     * Fetch the unique name of the view
     *
     * @return string
     */
    public function getViewName();
}
