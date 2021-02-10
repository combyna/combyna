<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Promoter;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use InvalidArgumentException;

/**
 * Class DelegatingWidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingWidgetNodePromoter implements DelegatorInterface, WidgetNodePromoterInterface
{
    /**
     * @var callable[]
     */
    private $widgetNodePromoters;

    /**
     * Adds a promoter for a new type of core widget
     *
     * @param WidgetNodeTypePromoterInterface $coreWidgetNodePromoter
     */
    public function addPromoter(WidgetNodeTypePromoterInterface $coreWidgetNodePromoter)
    {
        foreach ($coreWidgetNodePromoter->getTypeToPromoterCallableMap() as $type => $promoterCallable) {
            $this->widgetNodePromoters[$type] = $promoterCallable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function promoteWidget(
        $name,
        WidgetNodeInterface $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        if (!array_key_exists($widgetNode->getType(), $this->widgetNodePromoters)) {
            $coreWidgetTypes = array_keys($this->widgetNodePromoters);
            $lastCoreWidgetType = array_pop($coreWidgetTypes);

            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported widget type "%s" for promotion - supported types are "%s" or "%s"',
                    $widgetNode->getType(),
                    implode('", "', $coreWidgetTypes),
                    $lastCoreWidgetType
                )
            );
        }

        return $this->widgetNodePromoters[$widgetNode->getType()](
            $name,
            $widgetNode,
            $resourceRepository,
            $parentWidget
        );
    }
}
