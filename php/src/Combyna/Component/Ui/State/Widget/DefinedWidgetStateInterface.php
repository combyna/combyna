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

use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Interface DefinedWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DefinedWidgetStateInterface extends WidgetStateInterface
{
    const TYPE = 'defined-widget';

    /**
     * Adds another rendered widget as a child of this one
     *
     * @param string $childName
     * @param WidgetStateInterface $childWidget
     */
    public function addChildState($childName, WidgetStateInterface $childWidget);

    /**
     * Fetches the specified attribute, evaluated to a static for this rendered widget
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the bag does not contain the specified static
     */
    public function getAttribute($name);

    /**
     * Fetches the specified child widget state of this one
     *
     * @param string $name
     * @return WidgetStateInterface
     */
    public function getChildState($name);
}
