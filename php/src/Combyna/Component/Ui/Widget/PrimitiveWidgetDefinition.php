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
use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use LogicException;

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
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $valueBagModel;

    /**
     * @var callable[]
     */
    private $valueNameToProviderCallableMap;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param EventFactoryInterface $eventFactory
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param FixedStaticBagModelInterface $valueBagModel
     * @param callable[] $valueNameToProviderCallableMap
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        EventFactoryInterface $eventFactory,
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        FixedStaticBagModelInterface $valueBagModel,
        array $valueNameToProviderCallableMap
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->eventDefinitionReferenceCollection = $eventDefinitionReferenceCollection;
        $this->eventFactory = $eventFactory;
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiStateFactory = $uiStateFactory;
        $this->valueBagModel = $valueBagModel;
        $this->valueNameToProviderCallableMap = $valueNameToProviderCallableMap;
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
    public function createEvaluationContextForWidget(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState
    ) {
        return $this->uiEvaluationContextFactory->createPrimitiveWidgetEvaluationContext(
            $parentContext,
            $this,
            $widget,
            $widgetState->getAttributeStaticBag()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        try {
            $eventDefinition = $this->eventDefinitionReferenceCollection->getDefinitionByName($libraryName, $eventName);
        } catch (EventDefinitionNotReferencedException $exception) {
            throw new EventDefinitionNotReferencedByWidgetException(
                $libraryName,
                $eventName,
                $this->libraryName,
                $this->name
            );
        }

        return $this->eventFactory->createEvent($eventDefinition, $payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialStateForWidget(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        // Create a sub-evaluation context for the primitive widget itself,
        // so that its attributes may be fetched by default expressions for the widget values
        $primitiveWidgetSubEvaluationContext = $this->uiEvaluationContextFactory->createPrimitiveWidgetEvaluationContext(
            $evaluationContext,
            $this,
            $widget,
            $attributeStaticBag
        );

        /*
         * Evaluate the defaults for the values of this widget -
         * defaults can reference attributes of this widget,
         * so that eg. a TextBox widget's "text" value can use a "text" attribute as the default
         */
        $valueStaticBag = $this->valueBagModel->createDefaultStaticBag(
            $primitiveWidgetSubEvaluationContext
        );

        return $this->uiStateFactory->createDefinedPrimitiveWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates
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
     * Fetches the specified widget value from its provider
     *
     * @param string $valueName
     * @param string[]|int[] $widgetStatePath
     * @return StaticInterface
     */
    public function getWidgetValue($valueName, array $widgetStatePath)
    {
        if (!array_key_exists($valueName, $this->valueNameToProviderCallableMap)) {
            throw new LogicException(
                sprintf(
                    'No provider was installed for widget value "%s"',
                    $valueName
                )
            );
        }

        $valueProvider = $this->valueNameToProviderCallableMap[$valueName];
        $valueType = $this->valueBagModel->getStaticType($valueName);

        // Call the provider, passing the unique path to the widget state
        // (the widget state's path could be different to the widget's path,
        // if the widget is inside a repeater, as each repeated instance will get a different state)
        $valueStatic = $valueProvider($widgetStatePath);

        // Value providers must return a static as their result
        if (!$valueStatic instanceof StaticInterface) {
            throw new LogicException(
                sprintf(
                    'Provider for value "%s" must return a static, %s returned',
                    $valueName,
                    is_object($valueStatic) ? get_class($valueStatic) : gettype($valueStatic)
                )
            );
        }

        // Check that the provider returned a static of the type it declares that it returns
        if (!$valueType->allowsStatic($valueStatic)) {
            throw new LogicException(
                sprintf(
                    'Provider for value "%s" must return a [%s], %s returned',
                    $valueName,
                    $valueType->getSummary(),
                    get_class($valueStatic)
                )
            );
        }

        return $valueStatic;
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        return true;
    }
}
