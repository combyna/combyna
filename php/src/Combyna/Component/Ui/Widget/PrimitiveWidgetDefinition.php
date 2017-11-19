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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class PrimitiveWidgetDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetDefinition implements WidgetDefinitionInterface
{
    const TYPE = 'primitive';

    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var EventDefinitionReferenceCollectionInterface
     */
    private $eventDefinitionReferenceCollection;

    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var array
     */
    private $labels;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param EventFactoryInterface $eventFactory
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param array $labels
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        EventFactoryInterface $eventFactory,
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        array $labels = []
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->eventDefinitionReferenceCollection = $eventDefinitionReferenceCollection;
        $this->eventFactory = $eventFactory;
        $this->labels = $labels;
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->uiStateFactory = $uiStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag)
    {
        $this->attributeBagModel->assertValidStaticBag($attributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        $eventDefinition = $this->eventDefinitionReferenceCollection->getDefinitionByName($libraryName, $eventName);

        return $this->eventFactory->createEvent($eventDefinition, $payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag
    ) {
        return $this->uiStateFactory->createDefinedWidgetState(
            $widget,
            $attributeStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLabel($label)
    {
        return array_key_exists($label, $this->labels) && $this->labels[$label] === true;
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function validateAttributeExpressions(
//        ValidationContextInterface $validationContext,
//        ExpressionBagNode $expressionBagNode
//    ) {
//        $this->attributeBagModel->validateStaticExpressionBag(
//            $validationContext,
//            $expressionBagNode,
//            'attributes for core "' . $this->name . '" widget'
//        );
//    }
}
