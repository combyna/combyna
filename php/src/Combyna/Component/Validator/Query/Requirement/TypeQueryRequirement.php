<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Query\Requirement;

use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class TypeQueryRequirement
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeQueryRequirement implements QueryRequirementInterface
{
    /**
     * @var ResultTypeQueryInterface
     */
    private $query;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ResultTypeQueryInterface $query
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(
        ResultTypeQueryInterface $query,
        ValidationContextInterface $validationContext
    ) {
        $this->query = $query; // Not used yet
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode)
    {
        /**
         * Don't adopt the ACT node, as we're trying to query for a type
         * so we don't want to apply any validation for the node itself
         */
    }

    /**
     * {@inheritdoc}
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer)
    {
        return $typeDeterminer->determine($this->validationContext);
    }
}
