<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Signal\Validation\Constraint\SignalDefinitionExistsConstraint;

/**
 * Class SignalDefinitionReferenceNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionReferenceNode extends AbstractActNode
{
    const TYPE = 'signal-definition-reference';

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new SignalDefinitionExistsConstraint(
                $this->libraryName,
                $this->signalName
            )
        );
    }

    /**
     * Fetches the unique name of the library that defines this signal definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the unique name of the signal
     *
     * @return string
     */
    public function getSignalName()
    {
        return $this->signalName;
    }
}
