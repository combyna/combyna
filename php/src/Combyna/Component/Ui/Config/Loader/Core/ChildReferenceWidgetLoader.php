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
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Ui\Config\Act\ChildReferenceWidgetNode;
use Combyna\Component\Ui\Config\Act\InvalidCoreWidgetNode;
use Combyna\Component\Ui\Config\Loader\WidgetConfigParserInterface;

/**
 * Class ChildReferenceWidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidgetLoader implements ChildReferenceWidgetLoaderInterface
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
            ChildReferenceWidgetNode::WIDGET_TYPE => [$this, 'load']
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
                new NamedParameter('name', new StringParameterType('child name'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new InvalidCoreWidgetNode(ChildReferenceWidgetNode::WIDGET_TYPE, $name, $exception->getMessage());
        }

        $childName = $parsedArgumentBag->getNamedStringArgument('name');

        return new ChildReferenceWidgetNode(
            $childName,
            $captureStaticBagModelNode,
            $captureExpressionBagNode,
            $visibilityExpressionNode,
            $tagMap
        );
    }
}
