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

use Combyna\Component\Expression\Config\Act\VariableExpressionNode;
use Combyna\Component\Expression\VariableExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class VariableExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExpressionLoader implements ExpressionTypeLoaderInterface
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
        $variableName = $this->configParser->getElement($config, 'variable', 'variable expression');

        return new VariableExpressionNode($variableName);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return VariableExpression::TYPE;
    }
}
