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

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;

/**
 * Class ExpressionParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionParameterTypeParser implements ParameterTypeTypeParserInterface
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
     * Determines whether an argument is valid for the parameter
     *
     * @param ExpressionParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        ExpressionParameterType $type,
        $value
    ) {
        // Allow any array or string value for expressions: the parser should return a special type of expression
        // to represent an invalid expression format
        // TODO: Allow number/boolean literals to be specified as a shorthand?
        return is_array($value) || is_string($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            ExpressionParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            ExpressionParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param ExpressionParameterType $type
     * @param array|string $rawValue Allow any type of argument, but should be an array or string
     * @return ExpressionNodeInterface
     */
    public function parseArgument(
        ExpressionParameterType $type,
        $rawValue
    ) {
        return $this->expressionLoader->load($rawValue);
    }
}
