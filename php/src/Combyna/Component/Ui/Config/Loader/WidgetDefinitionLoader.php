<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoader;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\CoreWidgetDefinitionNode;

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
     * @var FixedStaticBagModelLoader
     */
    private $fixedStaticBagModelLoader;

    /**
     * @param ConfigParser $configParser
     * @param FixedStaticBagModelLoader $fixedStaticBagModelLoader
     */
    public function __construct(ConfigParser $configParser, FixedStaticBagModelLoader $fixedStaticBagModelLoader)
    {
        $this->configParser = $configParser;
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
            case CoreWidgetDefinitionNode::TYPE:
                return $this->loadCoreWidgetDefinition(
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
            'compound "' . $widgetDefinitionName . '" widget definition attribute model config'
        );

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributeModelConfig);

        return new CompoundWidgetDefinitionNode($libraryName, $widgetDefinitionName, $attributeBagModelNode);
    }

    /**
     * Loads a CoreWidgetDefinitionNode from its array config
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param array $widgetDefinitionConfig
     * @return CoreWidgetDefinitionNode
     */
    private function loadCoreWidgetDefinition(
        $libraryName,
        $widgetDefinitionName,
        array $widgetDefinitionConfig
    ) {
        $attributeModelConfig = $this->configParser->getElement(
            $widgetDefinitionConfig,
            'attributes',
            'core "' . $widgetDefinitionName . '" widget definition attribute model config'
        );

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributeModelConfig);

        return new CoreWidgetDefinitionNode($libraryName, $widgetDefinitionName, $attributeBagModelNode);
    }
}
