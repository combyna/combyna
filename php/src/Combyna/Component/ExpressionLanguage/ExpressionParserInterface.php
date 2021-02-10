<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage;

/**
 * Interface ExpressionParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionParserInterface
{
    const RELATIVE_CACHED_PARSER_PATH = '/ExpressionLanguage/PegExpressionParser.php';

    /**
     * Parses an expression string, returning an expression array structure
     *
     * @param string $expression
     * @return array
     */
    public function parse($expression);
}
