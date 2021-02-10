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

use Combyna\Component\ExpressionLanguage\Exception\ParseFailedException;
use RuntimeException;

/**
 * Class ExpressionParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionParser implements ExpressionParserInterface
{
    /**
     * @var string
     */
    private $cachePath;

    /**
     * @param string $cachePath
     */
    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;
    }

    /**
     * Parses an expression string, returning an expression array structure
     *
     * @param string $expression
     * @return array
     * @throws ParseFailedException
     */
    public function parse($expression)
    {
        $pegExpressionParserPath = $this->cachePath . self::RELATIVE_CACHED_PARSER_PATH;

        if (!is_file($pegExpressionParserPath) || !is_readable($pegExpressionParserPath)) {
            throw new RuntimeException('Unable to find compiled ExpressionLanguage parser');
        }

        require_once $pegExpressionParserPath;

        // Included just above rather than autoloaded
        $parser = new PegExpressionParser($expression);

        $result = $parser->match_Expression() ;

        if ($result === false) {
            throw new ParseFailedException('Could not parse expression string "' . $expression . '"');
        }

        if ($result['text'] !== $expression) {
            throw new ParseFailedException('Could not parse expression string "' . $expression . '"');
        }

        return $result['node'];
    }
}
