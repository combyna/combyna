<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment;

use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\IncorrectFunctionTypeException;
use Combyna\Component\Environment\Exception\LibraryAlreadyInstalledException;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Environment\Library\FunctionInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Router\RouteInterface;
use Combyna\Component\Signal\Exception\SignalDefinitionNotFoundException;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;

/**
 * Interface EnvironmentInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EnvironmentInterface extends ResourceRepositoryInterface
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
    public function getGenericFunctionByName($libraryName, $functionName);

    /**
     * Fetches a route from a library installed into this environment.
     * Throws a LibraryNotInstalled or WidgetDefinitionNotSupported exception on failure
     *
     * @param string $libraryName
     * @param string $routeName
     * @return RouteInterface
     * @throws LibraryNotInstalledException
     * @throws NotFoundException
     */
    public function getRouteByName($libraryName, $routeName);

    /**
     * Fetches a signal definition from a library installed into this environment.
     * Throws a LibraryNotInstalled or SignalDefinitionNotSupported exception on failure
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionInterface
     * @throws LibraryNotInstalledException
     * @throws SignalDefinitionNotFoundException
     */
    public function getSignalDefinitionByName($libraryName, $signalName);

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
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName);

    /**
     * Installs a new library into this environment
     *
     * @param LibraryInterface $library
     * @throws LibraryAlreadyInstalledException
     */
    public function installLibrary(LibraryInterface $library);

    /**
     * Translates a key for the current locale
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    public function translate($key, array $arguments = []);
}
