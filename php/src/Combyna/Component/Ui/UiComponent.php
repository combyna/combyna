<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class UiComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            new DelegateeTagDefinition(
                'combyna.core_widget_loader',
                'combyna.ui.loader.widget',
                'addCoreWidgetLoader'
            ),
            new DelegateeTagDefinition(
                'combyna.view_store_instruction_promoter',
                'combyna.ui.promoter.view_store_instruction_node',
                'addPromoter'
            ),
            new DelegateeTagDefinition(
                'combyna.widget_promoter',
                'combyna.ui.promoter.widget_node',
                'addPromoter'
            )
        ]));
    }
}
