<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna;

use Combyna\Component\App\AppComponent;
use Combyna\Component\Bag\BagComponent;
use Combyna\Component\Behaviour\BehaviourComponent;
use Combyna\Component\Common\CommonComponent;
use Combyna\Component\Config\ConfigComponent;
use Combyna\Component\Environment\EnvironmentComponent;
use Combyna\Component\Event\EventComponent;
use Combyna\Component\Expression\ExpressionComponent;
use Combyna\Component\ExpressionLanguage\ExpressionLanguageComponent;
use Combyna\Component\Framework\FrameworkComponent;
use Combyna\Component\Framework\Originators;
use Combyna\Component\Framework\Runtime;
use Combyna\Component\Instruction\InstructionComponent;
use Combyna\Component\Plugin\PluginComponent;
use Combyna\Component\Plugin\PluginInterface;
use Combyna\Component\Program\ProgramComponent;
use Combyna\Component\Renderer\RendererComponent;
use Combyna\Component\Router\RouterComponent;
use Combyna\Component\Signal\SignalComponent;
use Combyna\Component\Store\StoreComponent;
use Combyna\Component\Trigger\TriggerComponent;
use Combyna\Component\Type\TypeComponent;
use Combyna\Component\Ui\UiComponent;
use Combyna\Component\Validator\ValidatorComponent;
use Combyna\Plugin\Core\CorePlugin;
use RuntimeException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class CombynaBootstrap
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CombynaBootstrap implements CombynaBootstrapInterface
{
    /**
     * @var string
     */
    private $commonCachePath;

    /**
     * @var string
     */
    private $compiledContainerClass;

    /**
     * @var string
     */
    private $compiledContainerNamespace;

    /**
     * @var ContainerBuilder|null
     */
    private $containerBuilder = null;

    /**
     * @var string
     */
    private $containerCachePath;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var string
     */
    private $originator;

    /**
     * @var PluginInterface[]
     */
    private $plugins;

    /**
     * @var Runtime
     */
    private $runtime;

    /**
     * @param PluginInterface[] $plugins
     * @param string|null $originator One of the Originators::* constants
     * @param bool $debug
     * @param string|null $cachePath
     * @param string $compiledContainerNamespace
     * @param string $compiledContainerClass
     */
    public function __construct(
        array $plugins = [],
        $originator = null,
        $debug = false,
        $cachePath = null,
        $compiledContainerNamespace = 'Combyna\Container',
        $compiledContainerClass = 'CompiledCombynaContainer'
    ) {
        $originator = $originator !== null ?
            $originator :
            Originators::CLIENT;
        $plugins = $this->resolvePlugins($plugins, $originator);
        $cachePath = $cachePath !== null ?
            rtrim($cachePath, '/') :
            __DIR__ . '/../../dist';

        $this->commonCachePath = $cachePath . '/common';
        $this->compiledContainerClass = $compiledContainerClass;
        $this->compiledContainerNamespace = $compiledContainerNamespace;
        $this->containerCachePath = $cachePath . '/' . $originator;
        $this->debug = $debug;
        $this->originator = $originator;
        $this->plugins = $plugins;

        $this->runtime = new Runtime(
            array_merge(
                [
                    // Components
                    new AppComponent(),
                    new BagComponent(),
                    new BehaviourComponent(),
                    new CommonComponent(),
                    new ConfigComponent(),
                    new EventComponent(),
                    new FrameworkComponent(),
                    new EnvironmentComponent(),
                    new ExpressionComponent(),
                    new ExpressionLanguageComponent(),
                    new InstructionComponent(),
                    new PluginComponent(),
                    new ProgramComponent(),
                    new RendererComponent(),
                    new RouterComponent(),
                    new SignalComponent(),
                    new StoreComponent(),
                    new TriggerComponent(),
                    new TypeComponent(),
                    new UiComponent(),
                    new ValidatorComponent(),

                    // Plugins
                    new CorePlugin()
                ],
                $this->plugins
            )
        );
    }

    /**
     * Fetches all plugins and their sub-plugins where the sub-plugins
     * support the originator, recursively
     *
     * @param PluginInterface[] $plugins
     * @param string $originator
     * @return PluginInterface[]
     */
    private function resolvePlugins(array $plugins, $originator)
    {
        $resolvedPlugins = [];

        foreach ($plugins as $plugin) {
            $resolvedPlugins[] = $plugin;

            foreach ($plugin->getSubPlugins() as $subPlugin) {
                if (in_array($originator, $subPlugin->getSupportedOriginators(), true)) {
                    $resolvedPlugins = array_merge(
                        $resolvedPlugins,
                        $this->resolvePlugins(
                            [$subPlugin],
                            $originator
                        )
                    );
                }
            }
        }

        return $resolvedPlugins;
    }

    /**
     * {@inheritdoc}
     */
    public function configureContainer(
        $cachePath = null,
        $compiledContainerNamespace = null,
        $compiledContainerClass = null
    ) {
        if ($compiledContainerClass !== null) {
            $this->compiledContainerClass = $compiledContainerClass;
        }

        if ($compiledContainerNamespace !== null) {
            $this->compiledContainerNamespace = $compiledContainerNamespace;
        }

        if ($cachePath !== null) {
            $this->containerCachePath = $cachePath;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createContainer()
    {
        $containerConfigCache = new ConfigCache($this->getCompiledContainerPath(), $this->debug);

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = $this->getContainerBuilder();

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump([
                    'namespace' => $this->compiledContainerNamespace,
                    'class' => $this->compiledContainerClass
                ]),
                $containerBuilder->getResources()
            );
        }

        // Explicitly fetch the compiled container class module
        // rather than relying on the autoloader being configured correctly
        require_once $this->getCompiledContainerPath();

        $containerFqcn = $this->compiledContainerNamespace . '\\' . $this->compiledContainerClass;
        $container = new $containerFqcn();

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommonCachePath()
    {
        return $this->commonCachePath;
    }

    /**
     * Fetches the complete path to the compiled service container
     *
     * @return string
     */
    private function getCompiledContainerPath()
    {
        return sprintf(
            '%s/%s.php',
            $this->containerCachePath,
            $this->compiledContainerClass
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerBuilder()
    {
        // Make sure the container cache path exists and is writable
        if (!is_dir($this->containerCachePath)) {
            if (@mkdir($this->containerCachePath, 0777, true) === false && !is_dir($this->containerCachePath)) {
                throw new RuntimeException(sprintf(
                    'Unable to create the cache directory (%s)',
                    $this->containerCachePath
                ));
            }
        } elseif (!is_writable($this->containerCachePath)) {
            throw new RuntimeException(sprintf(
                'Unable to write in the cache directory (%s)',
                $this->containerCachePath
            ));
        }

        if ($this->containerBuilder === null) {
            // First time the container builder has been accessed - create it
            $this->containerBuilder = new ContainerBuilder(new ParameterBag([
                'combyna.cache_path' => $this->commonCachePath,
                'combyna.debug' => $this->debug
            ]));
            $this->runtime->compile($this->containerBuilder);

            // Compile Combyna's container, resolving any autowiring etc.
            $this->containerBuilder->compile();
        }

        return $this->containerBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerCachePath()
    {
        return $this->containerCachePath;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp()
    {
        // Fetch the container builder rather than the container itself - we don't want to dump
        // the compiled container here, because if this container is being merged into a parent one
        // (eg. in CombynaBundle) then we don't want to generate a separate compiled container module.
        $containerBuilder = $this->getContainerBuilder();

        $containerBuilder->get('combyna.cache.warmer')->warmUp($this->containerCachePath);
    }
}
