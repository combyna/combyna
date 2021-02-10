<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader\Expression;

use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;
use Combyna\Component\Ui\Config\Act\Expression\CaptureExpressionNode;

/**
 * Class CaptureExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'capture';

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
        $captureName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'capture name'
        );

        return new CaptureExpressionNode($captureName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
