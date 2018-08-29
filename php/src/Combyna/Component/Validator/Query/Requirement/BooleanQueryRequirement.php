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
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class BooleanQueryRequirement
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanQueryRequirement implements QueryRequirementInterface
{
    /**
     * @var BooleanQueryInterface
     */
    private $query;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param BooleanQueryInterface $query
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(
        BooleanQueryInterface $query,
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
         * Don't adopt the ACT node, as we're trying to query for a boolean
         * so we don't want to apply any validation for the node itself
         */
    }
}
