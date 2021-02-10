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
     * @var ExpressionConfigParserInterface
     */
    private $configParser;

    /**
     * @param ExpressionConfigParserInterface $configParser
     */
    public function __construct(ExpressionConfigParserInterface $configParser)
    {
        $this->configParser = $configParser;
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
