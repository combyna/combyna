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

use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use LogicException;

/**
 * Class TextParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextParameterTypeParser implements ParameterTypeTypeParserInterface
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
     * @param TextParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        TextParameterType $type,
        $value
    ) {
        if (!is_array($value)) {
            // Value should always be an array that represents a text static
            return false;
        }

        $expressionNode = $this->expressionLoader->load($value);

        return $expressionNode instanceof TextExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            TextParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            TextParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param TextParameterType $type
     * @param $rawValue
     * @return mixed
     */
    public function parseArgument(
        TextParameterType $type,
        $rawValue
    ) {
        $expressionNode = $this->expressionLoader->load($rawValue);

        if (!$expressionNode instanceof TextExpressionNode) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    TextExpressionNode::class,
                    get_class($expressionNode)
                )
            );
        }

        return $expressionNode->toNative();
    }
}
