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

use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface WidgetNodePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetNodePromoterInterface
{
    /**
     * Promotes a WidgetNode to a Widget
     *
     * @param string|int $name
     * @param WidgetNodeInterface $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return WidgetInterface
     */
    public function promoteWidget(
        $name,
        WidgetNodeInterface $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    );
}
