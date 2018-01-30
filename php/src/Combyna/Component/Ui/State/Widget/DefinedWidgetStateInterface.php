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
interface DefinedWidgetStateInterface extends WidgetStateInterface
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
     * Fetches the names of all attributes for this widget
     *
     * @return string[]
     */
    public function getAttributeNames();

    /**
     * Fetches the attribute bag for this widget
     *
     * @return StaticBagInterface
     */
    public function getAttributeStaticBag();

    /**
     * Fetches the names of the children of this widget
     *
     * @return string[]
     */
    public function getChildNames();

    /**
     * Fetches the specified child widget state of this one
     *
     * @param string $name
     * @return WidgetStateInterface
     */
    public function getChildState($name);
}
