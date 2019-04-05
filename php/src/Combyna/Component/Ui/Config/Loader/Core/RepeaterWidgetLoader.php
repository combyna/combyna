<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader\Core;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\OptionalParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Config\Parameter\Type\WidgetParameterType;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Ui\Config\Act\InvalidCoreWidgetNode;
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Ui\Config\Loader\WidgetConfigParserInterface;
use Combyna\Component\Ui\Widget\RepeaterWidget;

/**
 * Class RepeaterWidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetLoader implements RepeaterWidgetLoaderInterface
{
    /**
     * @var WidgetConfigParserInterface
     */
    private $configParser;

    /**
     * @param WidgetConfigParserInterface $configParser
     */
    public function __construct(WidgetConfigParserInterface $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionToLoaderCallableMap()
    {
        return [
            RepeaterWidgetNode::WIDGET_TYPE => [$this, 'load']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $name,
        array $widgetConfig,
        FixedStaticBagModelNodeInterface $captureStaticBagModelNode,
        ExpressionBagNode $captureExpressionBagNode,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tagMap = []
    ) {
        try {
            $parsedArgumentBag = $this->configParser->parseArguments($widgetConfig, [
                new NamedParameter('items', new ExpressionParameterType('items list')),
                new OptionalParameter(
                    new NamedParameter(
                        'index_variable',
                        new StringParameterType('index variable name')
                    )
                ),
                new NamedParameter(
                    'item_variable',
                    new StringParameterType('item variable name')
                ),
                new NamedParameter(
                    'repeated',
                    new WidgetParameterType(RepeaterWidget::REPEATED_WIDGET_NAME, 'repeated widget')
                )
            ]);
        } catch (ArgumentParseException $exception) {
            return new InvalidCoreWidgetNode(RepeaterWidgetNode::WIDGET_TYPE, $name, $exception->getMessage());
        }

        $itemListExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('items');
        $indexVariableName = $parsedArgumentBag->getNamedStringArgument('index_variable');
        $itemVariableName = $parsedArgumentBag->getNamedStringArgument('item_variable');
        $repeatedWidgetNode = $parsedArgumentBag->getNamedWidgetArgument('repeated');

        return new RepeaterWidgetNode(
            $itemListExpressionNode,
            $indexVariableName,
            $itemVariableName,
            $repeatedWidgetNode,
            $name,
            $captureStaticBagModelNode,
            $captureExpressionBagNode,
            $visibilityExpressionNode,
            $tagMap
        );
    }
}
