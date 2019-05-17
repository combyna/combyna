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

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class BuiltinExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BuiltinExpressionLoader implements ExpressionTypeLoaderInterface, DelegatorInterface
{
    /**
     * @var BuiltinLoaderInterface[]
     */
    private $builtinLoaders = [];

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
     * Adds a new loader for a builtin
     *
     * @param BuiltinLoaderInterface $builtinLoader
     */
    public function addBuiltinLoader(BuiltinLoaderInterface $builtinLoader)
    {
        $this->builtinLoaders[$builtinLoader->getBuiltinName()] = $builtinLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $builtinName = $this->configParser->getElement($config, 'name', 'builtin');

        if (!array_key_exists($builtinName, $this->builtinLoaders)) {
            return new UnknownExpressionNode(
                'No loader is registered for builtin "' . $builtinName . '"',
                new NullActNodeAdopter()
            );
        }

        return $this->builtinLoaders[$builtinName]->load($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'builtin';
    }
}
