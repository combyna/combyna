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

use Combyna\Component\Common\DelegatorInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use InvalidArgumentException;

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
            throw new InvalidArgumentException('No loader is registered for builtin "' . $builtinName . '"');
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
