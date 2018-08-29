<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\TextExpression;

/**
 * Class TextExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpressionLoader implements ExpressionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @param ConfigParser $configParser
     */
    public function __construct(ConfigParser $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $text = $this->configParser->getElement($config, 'text', 'text expression');

        return new TextExpressionNode($text);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return TextExpression::TYPE;
    }
}
