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
use Combyna\Component\Expression\Config\Act\UnknownExpressionTypeNode;
use Combyna\Component\ExpressionLanguage\Config\Act\UnparsableExpressionNode;
use Combyna\Component\ExpressionLanguage\Exception\ParseFailedException;
use Combyna\Component\ExpressionLanguage\ExpressionParserInterface;

/**
 * Class DelegatingExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingExpressionLoader implements ExpressionLoaderInterface, DelegatorInterface
{
    /**
     * @var ExpressionParserInterface
     */
    private $expressionParser;
    /**
     * @var ExpressionTypeLoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param ExpressionParserInterface $expressionParser
     */
    public function __construct(ExpressionParserInterface $expressionParser)
    {
        $this->expressionParser = $expressionParser;
    }

    /**
     * @param ExpressionTypeLoaderInterface $expressionTypeLoader
     */
    public function addLoader(ExpressionTypeLoaderInterface $expressionTypeLoader)
    {
        $this->loaders[$expressionTypeLoader->getType()] = $expressionTypeLoader;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        if (is_string($config)) {
            // Expression was provided as a string that needs to be parsed
            $expression = trim($config);

            if ($expression[0] !== '=') {
                // Expression is missing the required leading formula sign
                // Return a special "unparsable expression" for validation to fail with
                $config = [
                    'type' => UnparsableExpressionNode::TYPE,
                    'expression' => $expression
                ];
            } else {
                $expression = substr($expression, 1); // Strip the leading formula sign

                try {
                    $config = $this->expressionParser->parse($expression);
                } catch (ParseFailedException $exception) {
                    // Return a special "unparsable expression" for validation to fail with
                    $config = [
                        'type' => UnparsableExpressionNode::TYPE,
                        'expression' => $expression
                    ];
                }
            }
        }

        if (!is_array($config)) {
            // Return a special "unparsable expression" for validation to fail with
            $config = [
                'type' => UnparsableExpressionNode::TYPE,
                'expression' => $config
            ];
        }

        if (!array_key_exists('type', $config)) {
            // Missing "type" element
            return new UnknownExpressionTypeNode(null);
        }

        $type = $config['type'];

        if (!array_key_exists($type, $this->loaders)) {
            // No loader is registered for expressions of this type
            return new UnknownExpressionTypeNode($type);
        }

        return $this->loaders[$type]->load($config);
    }
}
