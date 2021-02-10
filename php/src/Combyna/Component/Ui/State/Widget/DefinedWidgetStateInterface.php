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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Interface DefinedWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DefinedWidgetStateInterface extends ParentWidgetStateInterface, WidgetStateInterface
{
    /**
     * Fetches the specified attribute for this widget, evaluated to a static
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the widget does not define the specified attribute
     */
    public function getAttribute($name);

    /**
     * Fetches the names of all attributes for the widget this state is for
     *
     * @return string[]
     */
    public function getAttributeNames();

    /**
     * Fetches the attribute bag for the widget this state is for
     *
     * @return StaticBagInterface
     */
    public function getAttributeStaticBag();

    /**
     * Fetches the names of the children of the widget this state is for
     *
     * @return string[]
     */
    public function getChildNames();

    /**
     * Fetches the specified value for this widget, wrapped as a static
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the widget does not define the specified value
     */
    public function getValue($name);
}
