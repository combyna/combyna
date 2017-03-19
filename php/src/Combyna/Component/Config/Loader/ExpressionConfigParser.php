<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Loader;

use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use InvalidArgumentException;

/**
 * Class ExpressionConfigParser
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionConfigParser
{
    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(ExpressionLoaderInterface $expressionLoader)
    {
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * Fetches the native value of the specified positional argument,
     * provided that it is defined and is of the specified static class
     *
     * @param array $config
     * @param int $position Zero-based position of the argument to fetch
     * @param string $requiredStaticType Static expression type that must be specified
     * @param string $context
     * @return mixed
     * @throws InvalidArgumentException Throws when the argument is not passed
     */
    public function getPositionalArgumentNative(array $config, $position, $requiredStaticType, $context)
    {
        if (!array_key_exists($position, $config)) {
            throw new InvalidArgumentException(
                'Missing required argument at position #' . $position . ' for ' . $context
            );
        }

        $expression = $this->expressionLoader->load($config[0]);

        if (!$expression instanceof StaticInterface) {
            throw new InvalidArgumentException(
                'Argument at position #' . $position . ' for ' . $context .
                ' must be of a static expression type but was of "' . $expression->getType() . '"'
            );
        }

        if ($expression->getType() !== $requiredStaticType) {
            throw new InvalidArgumentException(
                'Argument at position #' . $position . ' for ' . $context .
                ' must be of type "' . $requiredStaticType . '" but was of "' . $expression->getType() . '"'
            );
        }

        return $expression->toNative();
    }
}
