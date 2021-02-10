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

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Context\NullValidationContext;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class PromotionQueryRequirement
 *
 * Used during ACT node creation, where no validation takes place
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PromotionQueryRequirement implements QueryRequirementInterface
{
    /**
     * @var ActNodeInterface
     */
    private $nodeBeingPromoted;

    /**
     * @param ActNodeInterface $nodeBeingPromoted
     */
    public function __construct(ActNodeInterface $nodeBeingPromoted)
    {
        $this->nodeBeingPromoted = $nodeBeingPromoted;
    }

    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode)
    {
        /**
         * Don't adopt the ACT node, as during promotion
         * no validation should be taking place anyway
         */
    }

    /**
     * {@inheritdoc}
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer)
    {
        return $typeDeterminer->determine(new NullValidationContext());
    }
}
