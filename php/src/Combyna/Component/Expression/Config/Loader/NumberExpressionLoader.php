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
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\NumberExpression;

/**
 * Class NumberExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpressionLoader implements ExpressionTypeLoaderInterface
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
        $number = $this->configParser->getElement($config, 'number', 'number expression', 'number');

        return new NumberExpressionNode($number);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return NumberExpression::TYPE;
    }
}
