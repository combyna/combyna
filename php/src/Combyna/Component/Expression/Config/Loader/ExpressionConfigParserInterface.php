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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use InvalidArgumentException;

/**
 * Interface ExpressionConfigParserInterface
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionConfigParserInterface extends ConfigParserInterface
{
    const NAMED_ARGUMENTS = 'named-arguments';
    const POSITIONAL_ARGUMENTS = 'positional-arguments';

    /**
     * Fetches a static bag with all the named arguments for the expression
     *
     * @deprecated Use ::parseArguments(...) instead
     *
     * @param array $config
     * @return ExpressionBagNode
     */
    public function getNamedArgumentStaticBag(array $config);

    /**
     * Fetches the expression node of the specified positional argument,
     * provided that it is defined and is of the specified static class
     *
     * @deprecated Use ::parseArguments(...) instead
     *
     * @param array $config
     * @param int $position Zero-based position of the argument to fetch
     * @param string $context A description of the meaning of the argument
     * @return ExpressionNodeInterface
     */
    public function getPositionalArgument(array $config, $position, $context);

    /**
     * Fetches the native value of the specified positional argument,
     * provided that it is defined and is of the specified static class
     *
     * @deprecated Use ::parseArguments(...) instead
     *
     * @param array $config
     * @param int $position Zero-based position of the argument to fetch
     * @param string $requiredStaticType Static expression type that must be specified
     * @param string $context A description of the meaning of the argument
     * @return mixed
     * @throws InvalidArgumentException Throws when the argument is not passed
     */
    public function getPositionalArgumentNative(array $config, $position, $requiredStaticType, $context);
}
