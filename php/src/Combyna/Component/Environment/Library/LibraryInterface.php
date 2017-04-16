<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\IncorrectFunctionTypeException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Ui\WidgetDefinitionInterface;

/**
 * Interface LibraryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LibraryInterface
{
    /**
     * Fetches a generic function defined by this library
     *
     * @param string $functionName
     * @return FunctionInterface
     * @throws FunctionNotSupportedException
     * @throws IncorrectFunctionTypeException
     */
    public function getGenericFunction($functionName);

    /**
     * Fetches the unique name of this library
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches an associative array of translation locales to translations
     *
     * @return array
     */
    public function getTranslations();

    /**
     * Fetches a widget definition defined by this library
     *
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     * @throws WidgetDefinitionNotSupportedException
     */
    public function getWidgetDefinition($widgetDefinitionName);
}
