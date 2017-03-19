<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity\Evaluation;

use Combyna\Component\Entity\EntityInterface;

/**
 * Interface EntityEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityEvaluationContextInterface
{
    /**
     * Fetches the entity this context is of
     *
     * @return EntityInterface
     */
    public function getEntity();
}
