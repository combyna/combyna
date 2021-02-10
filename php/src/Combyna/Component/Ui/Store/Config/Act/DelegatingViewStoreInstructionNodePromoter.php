<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Ui\Store\Instruction\InstructionFactoryInterface;
use InvalidArgumentException;

/**
 * Class DelegatingViewStoreInstructionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingViewStoreInstructionNodePromoter implements ViewStoreInstructionNodePromoterInterface, DelegatorInterface
{
    /**
     * @var InstructionFactoryInterface
     */
    private $instructionFactory;

    /**
     * @var callable[]
     */
    private $instructionPromoters = [];

    /**
     * @param InstructionFactoryInterface $instructionFactory
     */
    public function __construct(InstructionFactoryInterface $instructionFactory)
    {
        $this->instructionFactory = $instructionFactory;
    }

    /**
     * Adds a promoter for a new type of instruction node type
     *
     * @param ViewStoreInstructionNodeTypePromoterInterface $instructionNodePromoter
     */
    public function addPromoter(ViewStoreInstructionNodeTypePromoterInterface $instructionNodePromoter)
    {
        foreach ($instructionNodePromoter->getTypeToPromoterMethodMap() as $type => $promoterMethod) {
            $this->instructionPromoters[$type] = [
                $instructionNodePromoter,
                $promoterMethod
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ViewStoreInstructionNodeInterface $instructionNode)
    {
        if (!array_key_exists($instructionNode->getType(), $this->instructionPromoters)) {
            throw new InvalidArgumentException(
                'No promoter for instructions of type "' . $instructionNode->getType() . '" is registered'
            );
        }

        return $this->instructionPromoters[$instructionNode->getType()]($instructionNode);
    }

    /**
     * {@inheritdoc}
     */
    public function promoteList(array $instructionNodes)
    {
        $instructions = [];

        foreach ($instructionNodes as $instructionNode) {
            $instructions[] = $this->promote($instructionNode);
        }

        return $this->instructionFactory->createViewStoreInstructionList($instructions);
    }
}
