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

use Combyna\Component\Expression\Config\Act\ConcatenationExpressionNode;
use Combyna\Component\Expression\ConcatenationExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class ConcatenationExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionLoader implements ExpressionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ExpressionLoaderInterface $expressionLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $operandListConfig = $this->configParser->getElement($config, 'operands', 'operand list expression');

        $operandListExpression = $this->expressionLoader->load($operandListConfig);

        return new ConcatenationExpressionNode(
            $operandListExpression,
            array_key_exists($config, 'glue') ?
                $this->expressionLoader->load($config['glue']) :
                null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return ConcatenationExpression::TYPE;
    }
}
