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

use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Ui\WidgetDefinitionInterface;
use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\IncorrectFunctionTypeException;
use Combyna\Component\Environment\Exception\LibraryAlreadyInstalledException;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Library\FunctionInterface;
use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Interface EnvironmentInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EnvironmentInterface
{
    /**
     * Fetches a generic function from a library installed into this environment.
     * Throws a LibraryNotInstalled or FunctionNotSupported exception on failure
     *
     * @param string $libraryName
     * @param string $functionName
     * @return FunctionInterface
     * @throws LibraryNotInstalledException
     * @throws FunctionNotSupportedException
     * @throws IncorrectFunctionTypeException
     */
    public function getGenericFunction($libraryName, $functionName);

    /**
     * Fetches a UI widget definition from a library installed into this environment.
     * Throws a LibraryNotInstalled or WidgetDefinitionNotSupported exception on failure
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     * @throws LibraryNotInstalledException
     * @throws WidgetDefinitionNotSupportedException
     */
    public function getWidgetDefinition($libraryName, $widgetDefinitionName);

    /**
     * Installs a new library into this environment
     *
     * @param LibraryInterface $library
     * @throws LibraryAlreadyInstalledException
     */
    public function installLibrary(LibraryInterface $library);
}
