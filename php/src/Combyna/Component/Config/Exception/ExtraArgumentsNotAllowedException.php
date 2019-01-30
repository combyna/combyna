<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Exception;

use Exception;

/**
 * Class ExtraArgumentsNotAllowedException
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExtraArgumentsNotAllowedException extends Exception
{
    /**
     * @param array $extraArguments
     */
    public function __construct(array $extraArguments)
    {
        parent::__construct(sprintf('Extra arguments not allowed: [%s]',
            implode(', ', array_keys($extraArguments))
        ));
    }
}
