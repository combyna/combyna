<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Loader;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Expression\Config\Act\StaticNodeInterface;
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
    const NAMED_ARGUMENTS = 'named-arguments';
    const POSITIONAL_ARGUMENTS = 'positional-arguments';

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(
        ExpressionLoaderInterface $expressionLoader,
        ExpressionBagLoaderInterface $expressionBagLoader,
        BagFactoryInterface $bagFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->expressionBagLoader = $expressionBagLoader;
        $this->expressionLoader = $expressionLoader;
    }

//    /**
//     * Fetches the native value of the specified named argument,
//     * provided that it is defined and is of the specified static class
//     *
//     * @param array $config
//     * @param string $name Unique name of the argument to fetch
//     * @param string $requiredStaticType Static expression type that must be specified
//     * @param string $context A description of the meaning of the argument
//     * @return mixed
//     * @throws InvalidArgumentException Throws when the argument is not passed
//     */
//    public function getNamedArgumentStatic(array $config, $name, $requiredStaticType, $context)
//    {
//        $namedArgumentConfig = $config[self::NAMED_ARGUMENTS];
//
//        $expressionNode = $this->expressionLoader->load($namedArgumentConfig[$name]);
//
//        if (!$expressionNode instanceof StaticNodeInterface) {
//            throw new InvalidArgumentException(
//                'Argument with name "' . $name . '" for ' . $context .
//                ' must be of a static expression type but was of "' . $expressionNode->getType() . '"'
//            );
//        }
//
//        if ($expressionNode->getType() !== $requiredStaticType) {
//            throw new InvalidArgumentException(
//                'Argument with name "' . $name . '" for ' . $context .
//                ' must be of type "' . $requiredStaticType . '" but was of "' . $expressionNode->getType() . '"'
//            );
//        }
//
//        return $expressionNode->toNative();
//    }

    /**
     * Fetches a static bag with all the named arguments for the expression
     *
     * @param array $config
     * @return ExpressionBagNode
     */
    public function getNamedArgumentStaticBag(array $config)
    {
        $namedArgumentConfig = $config[self::NAMED_ARGUMENTS];

        return $this->expressionBagLoader->load($namedArgumentConfig);
    }

    /**
     * Fetches the native value of the specified positional argument,
     * provided that it is defined and is of the specified static class
     *
     * @param array $config
     * @param int $position Zero-based position of the argument to fetch
     * @param string $requiredStaticType Static expression type that must be specified
     * @param string $context A description of the meaning of the argument
     * @return mixed
     * @throws InvalidArgumentException Throws when the argument is not passed
     */
    public function getPositionalArgumentNative(array $config, $position, $requiredStaticType, $context)
    {
        $positionalArgumentConfig = $config[self::POSITIONAL_ARGUMENTS];

        if (!array_key_exists($position, $positionalArgumentConfig)) {
            throw new InvalidArgumentException(
                'Missing required argument at position #' . $position . ' for ' . $context
            );
        }

        $expressionNode = $this->expressionLoader->load($positionalArgumentConfig[$position]);

        if (!$expressionNode instanceof StaticNodeInterface) {
            throw new InvalidArgumentException(
                'Argument at position #' . $position . ' for ' . $context .
                ' must be of a static expression type but was of "' . $expressionNode->getType() . '"'
            );
        }

        if ($expressionNode->getType() !== $requiredStaticType) {
            throw new InvalidArgumentException(
                'Argument at position #' . $position . ' for ' . $context .
                ' must be of type "' . $requiredStaticType . '" but was of "' . $expressionNode->getType() . '"'
            );
        }

        return $expressionNode->toNative();
    }
}
