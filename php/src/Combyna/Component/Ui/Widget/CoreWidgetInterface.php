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

use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Interface CoreWidgetInterface
 *
 * Core widgets are eg. "group", "text" or "repeater"
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CoreWidgetInterface extends WidgetInterface
{
    const LIBRARY = LibraryInterface::CORE;
}
