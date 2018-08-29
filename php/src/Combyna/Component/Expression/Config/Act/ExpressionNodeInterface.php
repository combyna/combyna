<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Interface ExpressionNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionNodeInterface extends ActNodeInterface
{
    /**
     * Fetches a determiner for the type this expression will evaluate to
     *
     * @return TypeDeterminerInterface
     */
    public function getResultTypeDeterminer();

    /**
     * Fetches the type of expression, eg. `text`
     *
     * @return string
     */
    public function getType();
}
