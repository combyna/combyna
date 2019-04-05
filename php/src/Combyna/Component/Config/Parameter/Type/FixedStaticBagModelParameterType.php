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
 * Class FixedStaticBagModelParameterType
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModelParameterType implements ParameterTypeInterface
{
    const TYPE = 'fixed-static-bag-model';

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
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'a fixed static bag model for ' . $this->contextDescription;
    }
}
