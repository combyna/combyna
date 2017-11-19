<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Act;

use Combyna\Component\Common\DelegatorInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Trigger\Instruction\InstructionFactoryInterface;
use InvalidArgumentException;

/**
 * Class DelegatingInstructionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingInstructionNodePromoter implements InstructionNodePromoterInterface, DelegatorInterface
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
     * @param InstructionNodeTypePromoterInterface $instructionNodePromoter
     */
    public function addPromoter(InstructionNodeTypePromoterInterface $instructionNodePromoter)
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
    public function promote(InstructionNodeInterface $instructionNode, ResourceRepositoryInterface $resourceRepository)
    {
        if (!array_key_exists($instructionNode->getType(), $this->instructionPromoters)) {
            throw new InvalidArgumentException(
                'No promoter for instructions of type "' . $instructionNode->getType() . '" is registered'
            );
        }

        return $this->instructionPromoters[$instructionNode->getType()]($instructionNode, $resourceRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function promoteList(array $instructionNodes, ResourceRepositoryInterface $resourceRepository)
    {
        $instructions = [];

        foreach ($instructionNodes as $instructionNode) {
            $instructions[] = $this->promote($instructionNode, $resourceRepository);
        }

        return $this->instructionFactory->createInstructionList($instructions);
    }
}
