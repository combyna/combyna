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
 * Class RootSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootSubValidationContext implements RootSubValidationContextInterface
{
    /**
     * @var ActNodeInterface
     */
    private $rootNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $rootNodeBehaviourSpec;

    /**
     * @param ActNodeInterface $rootNode
     * @param BehaviourSpecInterface $rootNodeBehaviourSpec
     */
    public function __construct(
        ActNodeInterface $rootNode,
        BehaviourSpecInterface $rootNodeBehaviourSpec
    ) {
        $this->rootNode = $rootNode;
        $this->rootNodeBehaviourSpec = $rootNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getActNode()
    {
        return $this->rootNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->rootNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [];
    }
}
