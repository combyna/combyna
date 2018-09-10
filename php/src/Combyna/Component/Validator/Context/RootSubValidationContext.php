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

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
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
     * @var StructuredNodeInterface
     */
    private $subjectNode;

    /**
     * @param ActNodeInterface $rootNode
     * @param BehaviourSpecInterface $rootNodeBehaviourSpec
     * @param StructuredNodeInterface $subjectNode
     */
    public function __construct(
        ActNodeInterface $rootNode,
        BehaviourSpecInterface $rootNodeBehaviourSpec,
        StructuredNodeInterface $subjectNode
    ) {
        $this->rootNode = $rootNode;
        $this->rootNodeBehaviourSpec = $rootNodeBehaviourSpec;
        $this->subjectNode = $subjectNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
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

    /**
     * {@inheritdoc}
     */
    public function getSubjectActNode()
    {
        return $this->subjectNode;
    }
}
