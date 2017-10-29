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

use Combyna\Component\Config\Loader\ExpressionConfigParser;
use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\TextExpression;

/**
 * Class AssuredExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'assured';

    /**
     * @var ExpressionConfigParser
     */
    private $configParser;

    /**
     * @param ExpressionConfigParser $configParser
     */
    public function __construct(ExpressionConfigParser $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $assuredStaticName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            TextExpression::TYPE,
            'assured static'
        );

        return new AssuredExpressionNode($assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
