<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;

/**
 * Class ActNodeSubValidationContext
 *
 * Default validation context created while validating any ACT node
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ActNodeSubValidationContext implements ActNodeSubValidationContextInterface
{
    /**
     * @var ActNodeInterface
     */
    private $actNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $behaviourSpec;

    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        $this->actNode = $actNode;
        $this->behaviourSpec = $behaviourSpec;
        $this->parentContext = $parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getActNode()
    {
        return $this->actNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->behaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        return $this->parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $path = $this->parentContext->getPath();

        if ($path !== '') {
            $path .= '.';
        }

        $path .= '[' . $this->actNode->getIdentifier() . ']';

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [];
    }
}
