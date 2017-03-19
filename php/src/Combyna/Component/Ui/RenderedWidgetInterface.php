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
 * Interface RenderedWidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RenderedWidgetInterface
{
    /**
     * Fetches the specified attribute, evaluated to a static for this rendered widget
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the bag does not contain the specified static
     */
    public function getAttribute($name);

    /**
     * Fetches the specified rendered child widget of this one
     *
     * @return RenderedWidgetInterface
     */
    public function getChildWidget($name);

    /**
     * Fetches the name of the library this widget's definition belongs to
     *
     * @return string
     */
    public function getWidgetDefinitionLibraryName();

    /**
     * Fetches the unique name of the definition for this widget
     *
     * @return string
     */
    public function getWidgetDefinitionName();

    /**
     * Fetch the unique path to the widget
     *
     * @return string
     */
    public function getWidgetPath();
}
