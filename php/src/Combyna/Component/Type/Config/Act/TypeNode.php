<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class TypeNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeNode extends AbstractActNode
{
    const TYPE = 'type';

    /**
     * @var TypeDeterminerInterface
     */
    private $typeDeterminer;

    /**
     * @param TypeDeterminerInterface $typeDeterminer
     */
    public function __construct(TypeDeterminerInterface $typeDeterminer)
    {
        $this->typeDeterminer = $typeDeterminer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->typeDeterminer->getStructuredChildNodes() as $structuredChildNode) {
            $specBuilder->addChildNode($structuredChildNode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getType() . ':' . $this->typeDeterminer->getSummary();
    }

    /**
     * Fetches the type determiner for this node
     *
     * @return TypeDeterminerInterface
     */
    public function getTypeDeterminer()
    {
        return $this->typeDeterminer;
    }
}
