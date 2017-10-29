<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\View;

use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Interface EmbedViewStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EmbedViewStateInterface extends ViewStateInterface
{
    /**
     * Fetches the specified attribute, evaluated to a static for this view state
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the bag does not contain the specified static
     */
    public function getAttribute($name);
}
