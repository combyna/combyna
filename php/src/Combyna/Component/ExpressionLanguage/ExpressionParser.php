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

use InvalidArgumentException;

/**
 * Class ExpressionParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionParser
{
    /**
     * Parses an expression string, returning an expression array structure
     *
     * @param string $expression
     * @return array
     */
    public function parse($expression)
    {
        $parser = new PegExpressionParser($expression);

        $result = $parser->match_Expression() ;

        if ($result === false) {
            throw new InvalidArgumentException('Could not parse expression string "' . $expression . '"');
        }

        if ($result['text'] !== $expression) {
            throw new InvalidArgumentException('Could not parse expression string "' . $expression . '"');
        }

        return $result['node'];
    }
}
