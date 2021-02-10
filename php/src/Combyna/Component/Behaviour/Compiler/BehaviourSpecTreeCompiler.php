<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Compiler;

use Combyna\Component\Behaviour\Compiler\Pass\BehaviourSpecPassInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecTreeWalkerInterface;
use Combyna\Component\Validator\Constraint\DelegatingConstraintValidatorInterface;

/**
 * Class BehaviourSpecTreeCompiler
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BehaviourSpecTreeCompiler implements BehaviourSpecTreeCompilerInterface
{
    /**
     * @var BehaviourSpecPassInterface[]
     */
    private $behaviourSpecPasses = [];

    /**
     * @var DelegatingConstraintValidatorInterface
     */
    private $delegatingConstraintValidator;

    /**
     * @var BehaviourSpecTreeWalkerInterface
     */
    private $specTreeWalker;

    /**
     * @param BehaviourSpecTreeWalkerInterface $specTreeWalker
     * @param DelegatingConstraintValidatorInterface $delegatingConstraintValidator
     */
    public function __construct(
        BehaviourSpecTreeWalkerInterface $specTreeWalker,
        DelegatingConstraintValidatorInterface $delegatingConstraintValidator
    ) {
        $this->delegatingConstraintValidator = $delegatingConstraintValidator;
        $this->specTreeWalker = $specTreeWalker;
    }

    /**
     * {@inheritdoc}
     */
    public function addSpecPass(BehaviourSpecPassInterface $behaviourSpecPass)
    {
        $this->behaviourSpecPasses[get_class($behaviourSpecPass)] = $behaviourSpecPass;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(BehaviourSpecInterface $rootSpec)
    {
        // Fetch a list of all passes used by constraint validators for behaviour specs' constraints
        // so that only the passes actually used are run, rather than every registered one
        $behaviourSpecPassClassNames = $this->delegatingConstraintValidator->getBehaviourSpecPassesForConstraints(
            $rootSpec->getAllConstraintClassesUsed()
        );

        $nodeClassToVisitorsCallableMap = [];

        foreach ($behaviourSpecPassClassNames as $behaviourSpecPassClassName) {
            $behaviourSpecPass = $this->behaviourSpecPasses[$behaviourSpecPassClassName];

            foreach ($behaviourSpecPass->getNodeClassToVisitorCallableMap() as $nodeClass => $visitorCallable) {
                $nodeClassToVisitorsCallableMap[$nodeClass][] = $visitorCallable;
            }
        }

        foreach ($rootSpec->getChildSpecs() as $childSpec) {
            $this->specTreeWalker->walk($childSpec, $nodeClassToVisitorsCallableMap, $rootSpec);
        }
    }
}
