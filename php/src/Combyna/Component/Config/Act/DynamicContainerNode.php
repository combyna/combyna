<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Validator\Constraint\CallbackConstraint;
use Combyna\Component\Validator\Context\NullValidationContext;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class DynamicContainerNode
 *
 * Represents an ACT node that can have child nodes added to it dynamically at runtime
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DynamicContainerNode extends AbstractActNode implements DynamicContainerNodeInterface
{
    const TYPE = 'dynamic';

    /**
     * @var DynamicActNodeInterface[]
     */
    private $adoptedChildren = [];

    /**
     * @var ValidationContextInterface|null
     */
    private $validationContext;

    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $childNode)
    {
        if ($this->validationContext !== null) {
            $this->validationContext->adoptDynamicActNode($childNode);
        } else {
            $this->adoptedChildren[] = $childNode;
        }

        return $childNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new CallbackConstraint(function (ValidationContextInterface $validationContext) {
                $this->validationContext = $validationContext;

                foreach ($this->adoptedChildren as $adoptedChild) {
                    $validationContext->adoptDynamicActNode($adoptedChild);
                }

                $this->adoptedChildren = [];
            })
        );
    }

    /**
     * {@inheritdoc}
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer)
    {
        $validationContext = $this->validationContext ?: new NullValidationContext();

        return $typeDeterminer->determine($validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
