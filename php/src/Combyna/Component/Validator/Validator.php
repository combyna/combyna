<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;

/**
 * Class Validator
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Validator implements ValidatorInterface
{
    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     */
    public function __construct(ValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ActNodeInterface $actNode, EnvironmentNode $environmentNode)
    {
        // Top-level context does not need to provide an expression as the current one,
        // each ACT node will create a sub-context for its own validation
        // with itself as the context node
        $validationContext = $this->validationFactory->createRootContext($environmentNode);

        $actNode->validate($validationContext);

        return $validationContext;
    }
}
