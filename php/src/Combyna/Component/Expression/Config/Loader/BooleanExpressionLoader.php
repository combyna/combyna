<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class BooleanExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpressionLoader implements ExpressionTypeLoaderInterface
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
        $boolean = $this->configParser->getElement($config, 'boolean', 'boolean expression');

        return new BooleanExpressionNode($boolean);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return BooleanExpression::TYPE;
    }
}
