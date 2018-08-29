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

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoader;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Event\Config\Loader\EventDefinitionReferenceLoaderInterface;
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\PrimitiveWidgetDefinitionNode;

/**
 * Class WidgetDefinitionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionLoader implements WidgetDefinitionLoaderInterface
{
    /**
     * @var ChildWidgetDefinitionCollectionLoaderInterface
     */
    private $childWidgetDefinitionCollectionLoader;

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var EventDefinitionReferenceLoaderInterface
     */
    private $eventDefinitionReferenceLoader;

    /**
     * @var FixedStaticBagModelLoader
     */
    private $fixedStaticBagModelLoader;

    /**
     * @var WidgetLoaderInterface
     */
    private $widgetLoader;

    /**
     * @param ConfigParser $configParser
     * @param ChildWidgetDefinitionCollectionLoaderInterface $childWidgetDefinitionCollectionLoader
     * @param FixedStaticBagModelLoader $fixedStaticBagModelLoader
     * @param EventDefinitionReferenceLoaderInterface $eventDefinitionReferenceLoader
     * @param WidgetLoaderInterface $widgetLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ChildWidgetDefinitionCollectionLoaderInterface $childWidgetDefinitionCollectionLoader,
        FixedStaticBagModelLoader $fixedStaticBagModelLoader,
        EventDefinitionReferenceLoaderInterface $eventDefinitionReferenceLoader,
        WidgetLoaderInterface $widgetLoader
    ) {
        $this->childWidgetDefinitionCollectionLoader = $childWidgetDefinitionCollectionLoader;
        $this->configParser = $configParser;
        $this->eventDefinitionReferenceLoader = $eventDefinitionReferenceLoader;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
        $this->widgetLoader = $widgetLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $libraryName,
        $widgetDefinitionName,
        array $widgetDefinitionConfig
    ) {
        $type = $this->configParser->getElement($widgetDefinitionConfig, 'type', 'widget definition type');

        switch ($type) {
            case CompoundWidgetDefinitionNode::TYPE:
                return $this->loadCompoundWidgetDefinition(
                    $libraryName,
                    $widgetDefinitionName,
                    $widgetDefinitionConfig
                );
            case PrimitiveWidgetDefinitionNode::TYPE:
                return $this->loadPrimitiveWidgetDefinition(
                    $libraryName,
                    $widgetDefinitionName,
                    $widgetDefinitionConfig
                );
            default:
                return new UnknownWidgetDefinitionTypeNode();
        }
    }

    /**
     * Loads a CompoundWidgetDefinitionNode from its array config
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param array $widgetDefinitionConfig
     * @return CompoundWidgetDefinitionNode
     */
    private function loadCompoundWidgetDefinition(
        $libraryName,
        $widgetDefinitionName,
        array $widgetDefinitionConfig
    ) {
        $attributeModelConfig = $this->configParser->getOptionalElement(
            $widgetDefinitionConfig,
            'attributes',
            'compound "' . $widgetDefinitionName . '" widget definition attribute model config',
            [],
            'array'
        );
        // Children that this widget expects to be passed
        $childDefinitionConfigs = $this->configParser->getOptionalElement(
            $widgetDefinitionConfig,
            'children',
            'compound "' . $widgetDefinitionName . '" widget definition supported children',
            [],
            'array'
        );
        // Types of event that this widget supports / is able to dispatch
        $eventDefinitionTypes = $this->configParser->getOptionalElement(
            $widgetDefinitionConfig,
            'events',
            'compound "' . $widgetDefinitionName . '" widget definition supported event types',
            [],
            'array'
        );
        $rootWidgetConfig = $this->configParser->getElement(
            $widgetDefinitionConfig,
            'root',
            'compound "' . $widgetDefinitionName . '" widget definition root widget config',
            'array'
        );

        $childDefinitionNodes = $this->childWidgetDefinitionCollectionLoader->loadCollection($childDefinitionConfigs);
        $eventDefinitionReferenceNodes = $this->eventDefinitionReferenceLoader->loadCollection($eventDefinitionTypes);

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributeModelConfig);
        $rootWidgetNode = $this->widgetLoader->loadWidget($rootWidgetConfig, 'root');

        return new CompoundWidgetDefinitionNode(
            $libraryName,
            $widgetDefinitionName,
            $attributeBagModelNode,
            $childDefinitionNodes,
            $eventDefinitionReferenceNodes,
            $rootWidgetNode
        );
    }

    /**
     * Loads a PrimitiveWidgetDefinitionNode from its array config
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param array $widgetDefinitionConfig
     * @return PrimitiveWidgetDefinitionNode
     */
    private function loadPrimitiveWidgetDefinition(
        $libraryName,
        $widgetDefinitionName,
        array $widgetDefinitionConfig
    ) {
        $attributeModelConfig = $this->configParser->getOptionalElement(
            $widgetDefinitionConfig,
            'attributes',
            'primitive "' . $widgetDefinitionName . '" widget definition attribute model config',
            [],
            'array'
        );
        // Children that this widget expects to be passed
        $childDefinitionConfigs = $this->configParser->getOptionalElement(
            $widgetDefinitionConfig,
            'children',
            'compound "' . $widgetDefinitionName . '" widget definition supported children',
            [],
            'array'
        );
        // Types of event that this widget supports / is able to dispatch
        $eventDefinitionTypes = $this->configParser->getOptionalElement(
            $widgetDefinitionConfig,
            'events',
            'primitive "' . $widgetDefinitionName . '" widget definition supported event types',
            [],
            'array'
        );

        $childDefinitionNodes = $this->childWidgetDefinitionCollectionLoader->loadCollection($childDefinitionConfigs);
        $eventDefinitionReferenceNodes = $this->eventDefinitionReferenceLoader->loadCollection($eventDefinitionTypes);

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributeModelConfig);

        return new PrimitiveWidgetDefinitionNode(
            $libraryName,
            $widgetDefinitionName,
            $attributeBagModelNode,
            $childDefinitionNodes,
            $eventDefinitionReferenceNodes
        );
    }
}
