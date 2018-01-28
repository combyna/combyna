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
     * @param ConfigParser $configParser
     * @param FixedStaticBagModelLoader $fixedStaticBagModelLoader
     * @param EventDefinitionReferenceLoaderInterface $eventDefinitionReferenceLoader
     */
    public function __construct(
        ConfigParser $configParser,
        FixedStaticBagModelLoader $fixedStaticBagModelLoader,
        EventDefinitionReferenceLoaderInterface $eventDefinitionReferenceLoader
    ) {
        $this->configParser = $configParser;
        $this->eventDefinitionReferenceLoader = $eventDefinitionReferenceLoader;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
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
        $attributeModelConfig = $this->configParser->getElement(
            $widgetDefinitionConfig,
            'attributes',
            'compound "' . $widgetDefinitionName . '" widget definition attribute model config',
            'array'
        );

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributeModelConfig);

        return new CompoundWidgetDefinitionNode(
            $libraryName,
            $widgetDefinitionName,
            $attributeBagModelNode
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
        $attributeModelConfig = $this->configParser->getElement(
            $widgetDefinitionConfig,
            'attributes',
            'core "' . $widgetDefinitionName . '" widget definition attribute model config',
            'array'
        );
        // Types of event that this widget supports / is able to dispatch
        $eventDefinitionTypes = $this->configParser->getElement(
            $widgetDefinitionConfig,
            'events',
            'core "' . $widgetDefinitionName . '" widget definition supported event types',
            'array'
        );

        $eventDefinitionReferenceNodes = $this->eventDefinitionReferenceLoader->loadCollection($eventDefinitionTypes);

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributeModelConfig);

        return new PrimitiveWidgetDefinitionNode(
            $libraryName,
            $widgetDefinitionName,
            $attributeBagModelNode,
            $eventDefinitionReferenceNodes
        );
    }
}
