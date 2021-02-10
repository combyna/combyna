<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ListElementTypeDeterminer
 *
 * Defines a type that is the combined type for all elements of a list
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListElementTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'list-element';

    /**
     * @var TypeDeterminerInterface
     */
    private $listTypeDeterminer;

    /**
     * @param TypeDeterminerInterface $listTypeDeterminer
     */
    public function __construct(TypeDeterminerInterface $listTypeDeterminer)
    {
        $this->listTypeDeterminer = $listTypeDeterminer;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $listType = $this->listTypeDeterminer->determine($validationContext);

        if ($listType instanceof ValuedType) {
            $listType = $listType->getWrappedType();
        }

        if ($listType instanceof StaticListType) {
            return $listType->getElementType();
        }

        // Determiner does not resolve to a list, so we cannot fetch a type for its elements
        return new UnresolvedType('list element type', $validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->listTypeDeterminer->makesQuery($querySpecifier);
    }
}
