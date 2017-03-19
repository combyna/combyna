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

/**
 * Interface ViewInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewInterface
{
    /**
     * Checks that the provided static bag is a valid set of attributes for this view
     *
     * @param StaticBagInterface $attributeStaticBag
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag);

    /**
     * Fetches the description of this view
     *
     * @return string
     */
    public function getDescription();

    /**
     * Fetches the unique name for this view
     *
     * @return string
     */
    public function getName();

    /**
     * Renders this view to a RenderedView
     *
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $rootEvaluationContext
     * @return RenderedViewInterface|null Returns the rendered view or null if invisible
     */
    public function render(
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $rootEvaluationContext
    );
}
