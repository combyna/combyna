<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;

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
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param ExpressionBagInterface $valueExpressionBag
     * @param WidgetInterface $rootWidget
     * @return CompoundWidgetDefinition
     */
    public function createCompoundWidgetDefinition(
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        ExpressionBagInterface $valueExpressionBag,
        WidgetInterface $rootWidget
    );

    /**
     * Creates a PrimitiveWidgetDefinition
     *
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param FixedStaticBagModelInterface $valueBagModel
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param array $valueNameToProviderCallableMap
     * @return PrimitiveWidgetDefinition
     */
    public function createPrimitiveWidgetDefinition(
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        FixedStaticBagModelInterface $valueBagModel,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        array $valueNameToProviderCallableMap
    );
}
