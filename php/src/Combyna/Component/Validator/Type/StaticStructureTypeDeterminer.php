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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StaticStructureTypeDeterminer
 *
 * Defines a type for a static structure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'structure';

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $attributeBagModelNode;

    /**
     * @var StaticStructureType|null
     */
    private $determinedType;

    /**
     * @param FixedStaticBagModelNodeInterface $attributeBagModelNode
     */
    public function __construct(FixedStaticBagModelNodeInterface $attributeBagModelNode)
    {
        $this->attributeBagModelNode = $attributeBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        if ($this->determinedType === null) {
            // A structure type cannot contain any sub-types that have not been determined,
            // so we need to determine them (recursively) at this point
            $determinedAttributeBagModel = $this->attributeBagModelNode->determine($validationContext);

            $this->determinedType = new StaticStructureType(
                $determinedAttributeBagModel,
                $validationContext
            );
        }

        return $this->determinedType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStructuredChildNodes()
    {
        return [
            $this->attributeBagModelNode
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->attributeBagModelNode->makesQuery($querySpecifier);
    }
}
