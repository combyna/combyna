<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Plugin;

use Combyna\Component\Framework\Originators;

/**
 * Class AbstractSubPlugin
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractSubPlugin extends AbstractPlugin implements SubPluginInterface
{
    /**
     * {@inheritdoc]
     */
    public function getSupportedOriginators()
    {
        return [
            Originators::CLIENT,
            Originators::SERVER
        ];
    }
}
