<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Trigger\Config\Loader\TriggerLoaderInterface;
use Combyna\Component\Ui\Config\Act\ChildReferenceWidgetNode;
use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Ui\Config\Act\TextWidgetNode;
use Combyna\Component\Ui\Config\Act\UnknownWidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetGroupNode;
use Combyna\Component\Ui\Widget\ConditionalWidget;
use Combyna\Component\Ui\Widget\RepeaterWidget;

/**
 * Class WidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetLoader implements WidgetLoaderInterface
{
    const CHILD_REFERENCE_NAME = 'child';
    const CONDITIONAL_NAME = 'conditional';
    const GROUP_NAME = 'group';
    const REPEATER_NAME = 'repeater';
    const TEXT_NAME = 'text';

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $fixedStaticBagModelLoader;

    /**
     * @var TriggerLoaderInterface
     */
    private $triggerLoader;

    /**
     * @var WidgetCollectionLoaderInterface
     */
    private $widgetCollectionLoader;

    /**
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     * @param ConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     * @param WidgetCollectionLoaderInterface $widgetCollectionLoader
     * @param TriggerLoaderInterface $triggerLoader
     */
    public function __construct(
        FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader,
        ConfigParser $configParser,
        ExpressionLoaderInterface $expressionLoader,
        ExpressionBagLoaderInterface $expressionBagLoader,
        WidgetCollectionLoaderInterface $widgetCollectionLoader,
        TriggerLoaderInterface $triggerLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionBagLoader = $expressionBagLoader;
        $this->expressionLoader = $expressionLoader;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
        $this->triggerLoader = $triggerLoader;
        $this->widgetCollectionLoader = $widgetCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadWidget(array $widgetConfig, $name = null)
    {
        // First check that the type of widget to create has been specified,
        // otherwise we cannot continue
        if (!array_key_exists('type', $widgetConfig)) {
            $keys = count($widgetConfig) > 0 ?
                '[empty array]' :
                '"' . implode('", "', array_keys($widgetConfig)) . '"';

            return new UnknownWidgetNode(sprintf(
                'Widget with no type defined - expected key "type", got "%s"',
                $keys
            ));
        }

        $type = $widgetConfig['type'];

        $visibilityExpressionNode = isset($widgetConfig['visible']) ?
            $this->expressionLoader->load($widgetConfig['visible']) :
            null;
        $tagNames = $this->configParser->getOptionalElement(
            $widgetConfig,
            'tags',
            'widget tags',
            [],
            'array'
        );

        // Definitions for captures within the context of the widget
        $captureStaticBagModelConfig = isset($widgetConfig['captures']) && isset($widgetConfig['captures']['define']) ?
            $widgetConfig['captures']['define'] :
            [];
        $captureStaticBagModelNode = $this->fixedStaticBagModelLoader->load($captureStaticBagModelConfig);

        // Expressions for captures defined by ancestors
        $captureExpressionBagConfig = isset($widgetConfig['captures']) && isset($widgetConfig['captures']['set']) ?
            $widgetConfig['captures']['set'] :
            [];
        $captureExpressionBagNode = $this->expressionBagLoader->load($captureExpressionBagConfig);

        if ($type === self::TEXT_NAME) {
            return new TextWidgetNode(
                $this->expressionLoader->load($widgetConfig['text']),
                $captureStaticBagModelNode,
                $captureExpressionBagNode,
                $visibilityExpressionNode,
                $this->buildTagMap($tagNames)
            );
        }

        if ($type === self::CHILD_REFERENCE_NAME) {
            return new ChildReferenceWidgetNode(
                $widgetConfig['name'],
                $captureStaticBagModelNode,
                $captureExpressionBagNode,
                $visibilityExpressionNode,
                $this->buildTagMap($tagNames)
            );
        }

        if ($type === self::CONDITIONAL_NAME) {
            $conditionConfig = $this->configParser->getElement(
                $widgetConfig,
                'condition',
                'condition',
                'array'
            );
            $consequentWidgetConfig = $this->configParser->getElement(
                $widgetConfig,
                'then',
                'consequent ("then") widget',
                'array'
            );
            $alternateWidgetConfig = $this->configParser->getOptionalElement(
                $widgetConfig,
                'else',
                'alternate ("else") widget',
                null,
                'array'
            );

            return new ConditionalWidgetNode(
                $this->expressionLoader->load($conditionConfig),
                $this->loadWidget($consequentWidgetConfig, ConditionalWidget::CONSEQUENT_WIDGET_NAME),
                $alternateWidgetConfig !== null ?
                    $this->loadWidget($alternateWidgetConfig, ConditionalWidget::ALTERNATE_WIDGET_NAME) :
                    null,
                $name,
                $captureStaticBagModelNode,
                $captureExpressionBagNode,
                $this->buildTagMap($tagNames)
            );
        }

        if ($type === self::REPEATER_NAME) {
            $itemListConfig = $this->configParser->getElement($widgetConfig, 'items', 'items list', 'array');
            $indexVariableName = $this->configParser->getOptionalElement($widgetConfig, 'index_variable', 'index variable');
            $itemVariableName = $this->configParser->getElement($widgetConfig, 'item_variable', 'index variable');
            $repeatedWidgetConfig = $this->configParser->getElement($widgetConfig, 'repeated', 'repeated widget', 'array');

            return new RepeaterWidgetNode(
                $this->expressionLoader->load($itemListConfig),
                $indexVariableName,
                $itemVariableName,
                $this->loadWidget($repeatedWidgetConfig, RepeaterWidget::REPEATED_WIDGET_NAME),
                $name,
                $captureStaticBagModelNode,
                $captureExpressionBagNode,
                $visibilityExpressionNode,
                $this->buildTagMap($tagNames)
            );
        }

        $attributeExpressionBag = $this->expressionBagLoader->load(
            isset($widgetConfig['attributes']) ?
                $widgetConfig['attributes'] :
                []
        );
        $childWidgets = isset($widgetConfig['children']) ?
            $this->widgetCollectionLoader->loadWidgets(
                $widgetConfig['children'],
                $this
            ) :
            [];
        $triggerNodes = $this->triggerLoader->loadCollection(
            $this->configParser->getOptionalElement(
                $widgetConfig,
                'triggers',
                'widget triggers',
                [],
                'array'
            )
        );

        if ($type === self::GROUP_NAME) {
            return new WidgetGroupNode(
                $childWidgets,
                $captureStaticBagModelNode,
                $captureExpressionBagNode,
                $name,
                $visibilityExpressionNode,
                $this->buildTagMap($tagNames)
            );
        }

        $parts = explode('.', $type, 2);

        if (count($parts) < 2) {
            return new UnknownWidgetNode(
                'Widget definition type must either be in format <library>.<name> or be one of the core types "child", "conditional", "group", "repeater" or "text" - "' . $type . '" given'
            );
        }

        list($libraryName, $widgetDefinitionName) = $parts;

        if ($libraryName === LibraryInterface::CORE) {
            // Core widget definitions eg. `group` or `text` must be used unprefixed,
            // eg. `group` rather than `core.group`
            return new UnknownWidgetNode(
                'Core widgets may not be used directly: tried to use "' . $type . '". ' .
                'Did you mean to use "' . $widgetDefinitionName . '"?'
            );
        }

        return new DefinedWidgetNode(
            $libraryName,
            $widgetDefinitionName,
            $attributeExpressionBag,
            $captureStaticBagModelNode,
            $captureExpressionBagNode,
            $name,
            $childWidgets,
            $triggerNodes,
            $visibilityExpressionNode,
            $this->buildTagMap($tagNames)
        );
    }

    /**
     * Builds up an associative array to speed up lookups
     *
     * @param string[] $tagNames
     * @return array
     */
    private function buildTagMap(array $tagNames)
    {
        $tags = [];

        foreach ($tagNames as $tagName) {
            $tags[$tagName] = true;
        }

        return $tags;
    }
}
