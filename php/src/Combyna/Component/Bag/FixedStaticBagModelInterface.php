<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface FixedStaticBagModelInterface
 *
 * Defines the static names and their types that a bag may store internally
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticBagModelInterface
{
    /**
     * Checks that the static is defined and matches its type for this model
     *
     * @param string $name
     * @param StaticInterface $value
     */
    public function assertValidStatic($name, StaticInterface $value);

    /**
     * Checks that all statics in the provided bag are defined and match their types for this model
     *
     * @param StaticBagInterface $staticBag
     */
    public function assertValidStaticBag(StaticBagInterface $staticBag);

    /**
     * Creates a StaticBag with all the default statics of definitions in this model
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticBagInterface
     */
    public function createDefaultStaticBag(EvaluationContextInterface $evaluationContext);

    /**
     * Returns true if this model defines a static with the specified name, false otherwise
     *
     * @param string $name
     * @return bool
     */
    public function definesStatic($name);
}
