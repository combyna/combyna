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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class PresolvedTypeDeterminer
 *
 * Defines a type that is already known
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PresolvedTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'presolved';

    /**
     * @var TypeInterface
     */
    private $presolvedType;

    /**
     * @param TypeInterface $presolvedType
     */
    public function __construct(TypeInterface $presolvedType)
    {
        $this->presolvedType = $presolvedType;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return $this->presolvedType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return parent::getSummary() . ':' . $this->presolvedType->getSummary();
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false; // Presolved types do not need to do any work, including queries
    }
}
