<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter\Type;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;

/**
 * Class ExpressionBagParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagParameterTypeParser implements ParameterTypeTypeParserInterface
{
    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     */
    public function __construct(ExpressionBagLoaderInterface $expressionBagLoader)
    {
        $this->expressionBagLoader = $expressionBagLoader;
    }

    /**
     * Determines whether an argument is valid for the parameter
     *
     * @param ExpressionBagParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        ExpressionBagParameterType $type,
        $value
    ) {
        return is_array($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            ExpressionBagParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            ExpressionBagParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param ExpressionBagParameterType $type
     * @param array $rawValue
     * @return ExpressionBagNode
     */
    public function parseArgument(
        ExpressionBagParameterType $type,
        array $rawValue
    ) {
        return $this->expressionBagLoader->load($rawValue);
    }
}
