<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression;

use Combyna\Bag\StaticListInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\StaticListType;
use Combyna\Type\TypeInterface;

/**
 * Class StaticListExpression
 *
 * Represents a list of static values
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListExpression extends AbstractStaticExpression
{
    const TYPE = 'static-list';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var StaticListInterface
     */
    private $staticList;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param StaticListInterface $staticList
     */
    public function __construct(ExpressionFactoryInterface $expressionFactory, StaticListInterface $staticList)
    {
        $this->expressionFactory = $expressionFactory;
        $this->staticList = $staticList;
    }

    /**
     * Returns a text static with all elements of the list concatenated together
     *
     * @return TextExpression
     */
    public function concatenate()
    {
        return $this->expressionFactory->createTextExpression($this->staticList->concatenate());
    }

    /**
     * Returns true if all the elements of this list match the provided type, false otherwise
     *
     * @param TypeInterface $type
     * @return bool
     */
    public function elementsMatch(TypeInterface $type)
    {
        return $this->staticList->elementsMatch($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        // Fetch a type that allows all elements in this list
        $elementType = $this->staticList->getElementType($validationContext);

        return new StaticListType($elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->staticList->toArray();
    }
}
