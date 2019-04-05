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
use Combyna\Component\Config\Parameter\Type\WidgetParameterType;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;
use Combyna\Component\Ui\Config\Act\InvalidCoreWidgetNode;
use Combyna\Component\Ui\Config\Loader\WidgetConfigParserInterface;
use Combyna\Component\Ui\Widget\ConditionalWidget;

/**
 * Class ConditionalWidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetLoader implements ConditionalWidgetLoaderInterface
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
            ConditionalWidgetNode::WIDGET_TYPE => [$this, 'load']
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
                new NamedParameter('condition', new ExpressionParameterType('condition')),
                new NamedParameter(
                    'then',
                    new WidgetParameterType(ConditionalWidget::CONSEQUENT_WIDGET_NAME, 'consequent ("then") widget')
                ),
                new OptionalParameter(
                    new NamedParameter(
                        'else',
                        new WidgetParameterType(ConditionalWidget::ALTERNATE_WIDGET_NAME, 'alternate ("else") widget')
                    )
                )
            ]);
        } catch (ArgumentParseException $exception) {
            return new InvalidCoreWidgetNode(ConditionalWidgetNode::WIDGET_TYPE, $name, $exception->getMessage());
        }

        $conditionExpressionNode = $parsedArgumentBag->getNamedExpressionArgument('condition');
        $consequentWidgetNode = $parsedArgumentBag->getNamedWidgetArgument('then');
        $alternateWidgetNode = $parsedArgumentBag->getNamedWidgetArgument('else');

        return new ConditionalWidgetNode(
            $conditionExpressionNode,
            $consequentWidgetNode,
            $alternateWidgetNode,
            $name,
            $captureStaticBagModelNode,
            $captureExpressionBagNode,
            $tagMap
        );
    }
}
