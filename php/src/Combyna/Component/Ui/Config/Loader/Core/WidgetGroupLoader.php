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
use Combyna\Component\Config\Parameter\Type\ArrayParameterType;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Ui\Config\Act\InvalidCoreWidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetGroupNode;
use Combyna\Component\Ui\Config\Loader\WidgetCollectionLoaderInterface;
use Combyna\Component\Ui\Config\Loader\WidgetConfigParserInterface;
use Combyna\Component\Ui\Config\Loader\WidgetLoaderInterface;

/**
 * Class WidgetGroupLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupLoader implements WidgetGroupLoaderInterface
{
    /**
     * @var WidgetConfigParserInterface
     */
    private $configParser;

    /**
     * @var WidgetCollectionLoaderInterface
     */
    private $widgetCollectionLoader;

    /**
     * @var WidgetLoaderInterface
     */
    private $widgetLoader;

    /**
     * @param WidgetConfigParserInterface $configParser
     * @param WidgetLoaderInterface $widgetLoader
     * @param WidgetCollectionLoaderInterface $widgetCollectionLoader
     */
    public function __construct(
        WidgetConfigParserInterface $configParser,
        WidgetLoaderInterface $widgetLoader,
        WidgetCollectionLoaderInterface $widgetCollectionLoader
    ) {
        $this->configParser = $configParser;
        $this->widgetCollectionLoader = $widgetCollectionLoader;
        $this->widgetLoader = $widgetLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionToLoaderCallableMap()
    {
        return [
            WidgetGroupNode::WIDGET_TYPE => [$this, 'load']
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
                new NamedParameter('children', new ArrayParameterType('child widgets'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new InvalidCoreWidgetNode(WidgetGroupNode::WIDGET_TYPE, $name, $exception->getMessage());
        }

        $childWidgetConfigs = $parsedArgumentBag->getNamedArrayArgument('children');

        $childWidgetNodes = $this->widgetCollectionLoader->loadWidgets($childWidgetConfigs, $this->widgetLoader);

        return new WidgetGroupNode(
            $childWidgetNodes,
            $captureStaticBagModelNode,
            $captureExpressionBagNode,
            $name,
            $visibilityExpressionNode,
            $tagMap
        );
    }
}
