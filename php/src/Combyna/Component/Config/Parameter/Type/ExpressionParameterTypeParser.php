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
        return is_array($value);
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
     * @param $rawValue
     * @return mixed
     */
    public function parseArgument(
        ExpressionParameterType $type,
        $rawValue
    ) {
        return $this->expressionLoader->load($rawValue);
    }
}
