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

use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class BinaryArithmeticExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionLoader implements ExpressionTypeLoaderInterface
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
        $leftOperandConfig = $this->configParser->getElement($config, 'left', 'binary arithmetic expression', 'array');
        $operator = $this->configParser->getElement($config, 'operator', 'binary arithmetic expression');
        $rightOperandConfig = $this->configParser->getElement($config, 'right', 'binary arithmetic expression', 'array');

        $leftOperandExpressionNode = $this->expressionLoader->load($leftOperandConfig);
        $rightOperandExpressionNode = $this->expressionLoader->load($rightOperandConfig);

        return new BinaryArithmeticExpressionNode(
            $leftOperandExpressionNode,
            $operator,
            $rightOperandExpressionNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return BinaryArithmeticExpression::TYPE;
    }
}
