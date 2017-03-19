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
 * Interface WidgetDefinitionFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionFactoryInterface
{
    /**
     * Creates a CompoundWidgetDefinition
     *
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @return CompoundWidgetDefinition
     */
    public function createCompoundWidgetDefinition(
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel
    );

    /**
     * Creates a CoreWidgetDefinition
     *
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @return CoreWidgetDefinition
     */
    public function createCoreWidgetDefinition(
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel
    );
}
