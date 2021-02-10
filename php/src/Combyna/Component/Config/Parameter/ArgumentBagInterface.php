<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter;

use Combyna\Component\Config\Exception\ExtraArgumentsNotCapturedException;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;

/**
 * Interface ArgumentBagInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ArgumentBagInterface
{
    /**
     * Fetches any extra arguments, assuming an ExtraParameter was defined
     *
     * @return array
     * @throws ExtraArgumentsNotCapturedException Throws when no extra parameter was defined
     */
    public function getExtraArguments();

    /**
     * Fetches a named, expression-type argument by its parameter name
     *
     * @param string $name
     * @return ExpressionNodeInterface
     */
    public function getNamedExpressionArgument($name);

    /**
     * Fetches a named, text-type argument by its parameter name
     *
     * @param string $name
     * @return string
     */
    public function getNamedTextArgument($name);
}
