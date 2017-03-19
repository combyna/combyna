<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use InvalidArgumentException;

/**
 * Interface ViewCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewCollectionInterface
{
    /**
     * Renders the specified view, or returns null if invisible
     *
     * @param string $viewName
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $rootEvaluationContext
     * @return RenderedViewInterface|null
     * @throws InvalidArgumentException Throws when the specified view does not exist
     */
    public function renderView(
        $viewName,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $rootEvaluationContext
    );
}
