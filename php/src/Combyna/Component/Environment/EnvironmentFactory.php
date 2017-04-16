<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment;

use Symfony\Component\Translation\Translator;

/**
 * Class EnvironmentFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentFactory implements EnvironmentFactoryInterface
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $libraries = [])
    {
        return new Environment($this->translator, $libraries);
    }
}
