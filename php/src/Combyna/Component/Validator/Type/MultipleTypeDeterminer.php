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
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class MultipleTypeDeterminer
 *
 * Defines a multiple type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MultipleTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'multiple';

    /**
     * @var TypeDeterminerInterface[]
     */
    private $subTypeDeterminers;

    /**
     * @param TypeDeterminerInterface[] $subTypeDeterminers
     */
    public function __construct(array $subTypeDeterminers)
    {
        $this->subTypeDeterminers = $subTypeDeterminers;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $subTypes = array_map(function (TypeDeterminerInterface $subTypeDeterminer) use ($validationContext) {
            return $subTypeDeterminer->determine($validationContext);
        }, $this->subTypeDeterminers);

        return new MultipleType($subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        foreach ($this->subTypeDeterminers as $subTypeDeterminer) {
            if ($subTypeDeterminer->makesQuery($querySpecifier)) {
                return true;
            }
        }

        return false;
    }
}
