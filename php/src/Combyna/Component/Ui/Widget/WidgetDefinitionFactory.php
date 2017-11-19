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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class WidgetDefinitionFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionFactory implements WidgetDefinitionFactoryInterface
{
    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $renderedWidgetFactory;

    /**
     * @param UiStateFactoryInterface $renderedWidgetFactory
     * @param EventFactoryInterface $eventFactory
     */
    public function __construct(UiStateFactoryInterface $renderedWidgetFactory, EventFactoryInterface $eventFactory)
    {
        $this->eventFactory = $eventFactory;
        $this->renderedWidgetFactory = $renderedWidgetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createCompoundWidgetDefinition(
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        array $labels = []
    ) {
        return new CompoundWidgetDefinition(
            $this->renderedWidgetFactory,
            $this->eventFactory,
            $eventDefinitionReferenceCollection,
            $libraryName,
            $name,
            $attributeBagModel,
            $labels
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPrimitiveWidgetDefinition(
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        array $labels = []
    ) {
        return new PrimitiveWidgetDefinition(
            $this->renderedWidgetFactory,
            $this->eventFactory,
            $eventDefinitionReferenceCollection,
            $libraryName,
            $name,
            $attributeBagModel,
            $labels
        );
    }
}
