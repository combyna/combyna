<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Validation\Context\Specifier;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;

/**
 * Class AppContextSpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppContextSpecifier implements SubValidationContextSpecifierInterface
{
    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @param EnvironmentNode $environmentNode
     */
    public function __construct(EnvironmentNode $environmentNode)
    {
        $this->environmentNode = $environmentNode;
    }

    /**
     * Fetches the EnvironmentNode for the app
     *
     * @return EnvironmentNode
     */
    public function getEnvironmentNode()
    {
        return $this->environmentNode;
    }
}
