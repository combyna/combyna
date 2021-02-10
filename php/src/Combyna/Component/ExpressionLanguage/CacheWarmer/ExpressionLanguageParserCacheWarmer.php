<?php

/**
 * Combyna Symfony bundle
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna-bundle
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna-bundle/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage\CacheWarmer;

use Combyna\Component\Common\Cache\CacheWarmerInterface;
use Combyna\Component\ExpressionLanguage\ExpressionParser;
use hafriedlander\Peg\Compiler as PegCompiler;
use RuntimeException;

/**
 * Class ExpressionLanguageParserCacheWarmer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionLanguageParserCacheWarmer implements CacheWarmerInterface
{
    /**
     * {@inheritdoc}
     */
    public function warmUp($cachePath)
    {
        $sourcePegPath = __DIR__ . '/../PegExpressionParser.php.inc';
        $cachedParserPath = $cachePath . ExpressionParser::RELATIVE_CACHED_PARSER_PATH;
        $cachedParserDir = dirname($cachedParserPath);

        if (!is_dir($cachedParserDir)) {
            if (@mkdir($cachedParserDir, 0777, true) === false) {
                throw new RuntimeException(
                    sprintf(
                        'Unable to create the Combyna ExpressionLanguage parser directory "%s".',
                        $cachedParserDir
                    )
                );
            }
        } elseif (!is_writable($cachedParserDir)) {
            throw new RuntimeException(
                sprintf(
                    'The Combyna ExpressionLanguage parser directory "%s" is not writable by the current system user.',
                    $cachedParserDir
                )
            );
        }

        PegCompiler::cli([
            '/bin/peg', // Fake argv[0]
            $sourcePegPath,
            $cachedParserPath
        ]);
    }
}
