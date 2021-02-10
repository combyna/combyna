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
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Ui\Config\Loader\CoreWidgetTypeLoaderInterface;

/**
 * Interface RepeaterWidgetLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RepeaterWidgetLoaderInterface extends CoreWidgetTypeLoaderInterface
{
    /**
     * Loads a RepeaterWidgetNode ACT node from its config array
     *
     * @param string|int $name
     * @param array $widgetConfig
     * @param FixedStaticBagModelNodeInterface $captureStaticBagModelNode
     * @param ExpressionBagNode $captureExpressionBagNode
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tagMap
     * @return RepeaterWidgetNode
     */
    public function load(
        $name,
        array $widgetConfig,
        FixedStaticBagModelNodeInterface $captureStaticBagModelNode,
        ExpressionBagNode $captureExpressionBagNode,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tagMap = []
    );
}
