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
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class CompoundWidgetDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinition implements WidgetDefinitionInterface
{
    const TYPE = 'compound';

    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     */
    public function __construct($libraryName, $name, FixedStaticBagModelInterface $attributeBagModel)
    {
        $this->attributeBagModel = $attributeBagModel;
        $this->name = $name;
        $this->libraryName = $libraryName;
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
//            'attributes for compound "' . $this->name . '" widget'
//        );
//    }
}
