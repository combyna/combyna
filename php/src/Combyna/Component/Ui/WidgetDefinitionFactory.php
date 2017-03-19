<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Bag\FixedStaticBagModelInterface;

/**
 * Class WidgetDefinitionFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionFactory implements WidgetDefinitionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createCompoundWidgetDefinition(
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel
    ) {
        return new CompoundWidgetDefinition($libraryName, $name, $attributeBagModel);
    }

    /**
     * {@inheritdoc}
     */
    public function createCoreWidgetDefinition(
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel
    ) {
        return new CoreWidgetDefinition($libraryName, $name, $attributeBagModel);
    }
}
