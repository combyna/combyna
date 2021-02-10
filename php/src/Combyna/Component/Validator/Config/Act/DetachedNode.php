<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Config\Act;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\Specifier\DetachedContextSpecifier;

/**
 * Class DetachedNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DetachedNode extends AbstractActNode
{
    const TYPE = 'detached';

    /**
     * @var StructuredNodeInterface
     */
    private $childNode;

    /**
     * @param StructuredNodeInterface $childNode
     */
    public function __construct(StructuredNodeInterface $childNode)
    {
        $this->childNode = $childNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->defineValidationContext(new DetachedContextSpecifier());

        $specBuilder->addChildNode($this->childNode);
    }

    /**
     * Fetches the root node of the detached ACT
     *
     * @return StructuredNodeInterface
     */
    public function getChild()
    {
        return $this->childNode;
    }
}
