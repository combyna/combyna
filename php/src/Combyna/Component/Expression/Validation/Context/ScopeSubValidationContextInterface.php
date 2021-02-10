<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Context;

use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface ScopeSubValidationContextInterface
 *
 * Represents a scope within which a group of variables are defined,
 * eg. the item and index variables inside a MapExpression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ScopeSubValidationContextInterface extends SubValidationContextInterface
{
}
