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
use Combyna\Component\Expression\Config\Act\NothingExpressionNode;
use Combyna\Component\Expression\NothingExpression;

/**
 * Class NothingExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NothingExpressionLoader implements ExpressionTypeLoaderInterface
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
        return new NothingExpressionNode();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return NothingExpression::TYPE;
    }
}
