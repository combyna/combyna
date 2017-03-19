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

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Ui\Config\Act\WidgetNode;
use InvalidArgumentException;

/**
 * Class WidgetLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetLoader implements WidgetLoaderInterface
{
    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var WidgetCollectionLoaderInterface
     */
    private $widgetCollectionLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     * @param WidgetCollectionLoaderInterface $widgetCollectionLoader
     */
    public function __construct(
        ExpressionLoaderInterface $expressionLoader,
        ExpressionBagLoaderInterface $expressionBagLoader,
        WidgetCollectionLoaderInterface $widgetCollectionLoader
    ) {
        $this->expressionBagLoader = $expressionBagLoader;
        $this->expressionLoader = $expressionLoader;
        $this->widgetCollectionLoader = $widgetCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadWidget(array $widgetConfig, EnvironmentNode $environmentNode)
    {
        $type = $widgetConfig['type'];
        $attributeExpressionBag = $this->expressionBagLoader->load($widgetConfig['attributes']);
        $childWidgets = $widgetConfig['children'] !== null ?
            $this->widgetCollectionLoader->loadWidgets(
                $widgetConfig['children'],
                $this,
                $environmentNode
            ) :
            [];
        $visibilityExpression = array_key_exists('visible', $widgetConfig) ?
            $this->expressionLoader->load($widgetConfig['visible']) :
            null;

        $parts = explode('.', $type, 2);

        if (count($parts) < 2) {
            throw new InvalidArgumentException(
                'Widget definition type must be in format <library>.<name>'
            );
        }

        list($libraryName, $widgetDefinitionName) = $parts;

        $widgetDefinitionNode = $environmentNode->getWidgetDefinition($libraryName, $widgetDefinitionName);

        return new WidgetNode(
            $widgetDefinitionNode,
            $attributeExpressionBag,
            $childWidgets,
            $visibilityExpression
        );
    }
}
