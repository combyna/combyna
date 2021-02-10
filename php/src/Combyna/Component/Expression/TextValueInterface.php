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
 * Interface TextValueInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TextValueInterface extends StaticValueInterface
{
    const MAX_SUMMARY_LENGTH = 20;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function toNative();
}
