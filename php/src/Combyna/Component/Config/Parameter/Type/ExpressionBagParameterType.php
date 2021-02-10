<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter\Type;

/**
 * Class ExpressionBagParameterType
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagParameterType implements ParameterTypeInterface
{
    const TYPE = 'expression-bag';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     */
    public function __construct($contextDescription)
    {
        $this->contextDescription = $contextDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'a bag of expressions for ' . $this->contextDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
