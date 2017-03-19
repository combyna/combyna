<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Ui\Config\Act\ViewCollectionNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AppNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppNode extends AbstractActNode
{
    const TYPE = 'app';

    /**
     * @var ViewCollectionNode
     */
    private $viewCollectionNode;

    /**
     * @param ViewCollectionNode $viewCollectionNode
     */
    public function __construct(ViewCollectionNode $viewCollectionNode)
    {
        $this->viewCollectionNode = $viewCollectionNode;
    }

    /**
     * Fetches the collection of views in the app
     *
     * @return ViewCollectionNode
     */
    public function getViewCollection()
    {
        return $this->viewCollectionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->viewCollectionNode->validate($subValidationContext);
    }
}
