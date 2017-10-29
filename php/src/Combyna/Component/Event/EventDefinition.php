<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class EventDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinition implements EventDefinitionInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $payloadStaticBagModel;

    /**
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $payloadStaticBagModel
     */
    public function __construct($libraryName, $name, FixedStaticBagModelInterface $payloadStaticBagModel)
    {
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->payloadStaticBagModel = $payloadStaticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidPayloadStaticBag(StaticBagInterface $payloadStaticBag)
    {
        $this->payloadStaticBagModel->assertValidStaticBag($payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel()
    {
        return $this->payloadStaticBagModel;
    }
}
