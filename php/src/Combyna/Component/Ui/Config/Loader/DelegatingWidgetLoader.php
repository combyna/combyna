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
use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Trigger\Config\Loader\TriggerLoaderInterface;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\UnknownWidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionReferenceNode;

/**
 * Class DelegatingWidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingWidgetLoader implements DelegatorInterface, WidgetLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var CoreWidgetTypeLoaderInterface[]
     */
    private $coreWidgetLoaderCallables = [];

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
     * Adds a loader for a type of core widget
     *
     * @param CoreWidgetTypeLoaderInterface $coreWidgetLoader
     */
    public function addCoreWidgetLoader(CoreWidgetTypeLoaderInterface $coreWidgetLoader)
    {
        foreach ($coreWidgetLoader->getWidgetDefinitionToLoaderCallableMap() as $type => $loaderCallable) {
            $this->coreWidgetLoaderCallables[$type] = $loaderCallable;
        }
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

        // TODO: Remove visibility expressions in favour of ConditionalWidgets
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

        $parts = explode('.', $type, 2);

        if (count($parts) === 1) {
            if (!array_key_exists($type, $this->coreWidgetLoaderCallables)) {
                $coreWidgetTypes = array_keys($this->coreWidgetLoaderCallables);
                $lastCoreWidgetType = array_pop($coreWidgetTypes);

                return new UnknownWidgetNode(
                    'Widget definition type must either be in format <library>.<name> or be one of the core types ' .
                    '"' . implode('", "', $coreWidgetTypes) . '" or "' . $lastCoreWidgetType . '" - "' . $type . '" given'
                );
            }

            return $this->coreWidgetLoaderCallables[$type](
                $name,
                $widgetConfig,
                $captureStaticBagModelNode,
                $captureExpressionBagNode,
                $visibilityExpressionNode,
                $this->buildTagMap($tagNames)
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

        return new DefinedWidgetNode(
            new WidgetDefinitionReferenceNode($libraryName, $widgetDefinitionName),
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
