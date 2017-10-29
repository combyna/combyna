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

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ExpressionConfigParser;
use Combyna\Component\Expression\Config\Act\TranslationExpressionNode;

/**
 * Class TranslationExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TranslationExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'trans';

    /**
     * @var ExpressionConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @param ExpressionConfigParser $configParser
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     */
    public function __construct(
        ExpressionConfigParser $configParser,
        ExpressionBagLoaderInterface $expressionBagLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionBagLoader = $expressionBagLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $translationKey = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'translation expression'
        );
        $argumentBagNode = $this->configParser->getNamedArgumentStaticBag($config);

        return new TranslationExpressionNode(
            $translationKey,
            $argumentBagNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
