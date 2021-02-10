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
use Combyna\Component\Ui\Config\Act\WidgetDefinitionReferenceNode;
use Combyna\Component\Ui\Widget\WidgetDefinitionReferenceInterface;

/**
 * Interface WidgetDefinitionReferenceNodePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionReferenceNodePromoterInterface
{
    /**
     * Creates a widget definition reference from its ACT node
     *
     * @param WidgetDefinitionReferenceNode $definitionReferenceNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @return WidgetDefinitionReferenceInterface
     */
    public function promote(
        WidgetDefinitionReferenceNode $definitionReferenceNode,
        ResourceRepositoryInterface $resourceRepository
    );
}
