<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

/**
 * Class SignalDefinitionReference
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionReference implements SignalDefinitionReferenceInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $libraryName
     * @param string $signalName
     */
    public function __construct($libraryName, $signalName)
    {
        $this->libraryName = $libraryName;
        $this->signalName = $signalName;
    }

    /**
     * @inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalName()
    {
        return $this->signalName;
    }
}
