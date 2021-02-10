<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

/**
 * Interface NumberValueInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface NumberValueInterface extends StaticValueInterface
{
    /**
     * {@inheritdoc}
     *
     * @return float|int
     */
    public function toNative();
}
