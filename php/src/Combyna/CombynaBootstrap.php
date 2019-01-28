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
use Combyna\Component\Config\ConfigComponent;
use Combyna\Component\Environment\EnvironmentComponent;
use Combyna\Component\Event\EventComponent;
use Combyna\Component\Expression\ExpressionComponent;
use Combyna\Component\ExpressionLanguage\ExpressionLanguageComponent;
use Combyna\Component\Framework\FrameworkComponent;
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
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

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
    private $compiledContainerClass;

    /**
     * @var string
     */
    private $compiledContainerNamespace;

    /**
     * @var string
     */
    private $compiledContainerPath;

    /**
     * @var PluginInterface[]
     */
    private $plugins;

    /**
     * @param PluginInterface[] $plugins
     * @param string|null $compiledContainerPath
     * @param string $compiledContainerNamespace
     * @param string $compiledContainerClass
     */
    public function __construct(
        array $plugins = [],
        $compiledContainerPath = null,
        $compiledContainerNamespace = 'Combyna\Container',
        $compiledContainerClass = 'CompiledCombynaContainer'
    ) {
        $this->compiledContainerClass = $compiledContainerClass;
        $this->compiledContainerNamespace = $compiledContainerNamespace;
        $this->compiledContainerPath = $compiledContainerPath !== null ?
            $compiledContainerPath :
            __DIR__ . '/../../dist/Combyna/Container/CompiledCombynaContainer.php';
        $this->plugins = $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function configureContainer(
        $compiledContainerPath = null,
        $compiledContainerNamespace = null,
        $compiledContainerClass = null
    ) {
        if ($compiledContainerClass !== null) {
            $this->compiledContainerClass = $compiledContainerClass;
        }

        if ($compiledContainerNamespace !== null) {
            $this->compiledContainerNamespace = $compiledContainerNamespace;
        }

        if ($compiledContainerPath !== null) {
            $this->compiledContainerPath = $compiledContainerPath;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer($isDebug = true)
    {
        $runtime = new Runtime(
            array_merge(
                [
                    // Components
                    new AppComponent(),
                    new BagComponent(),
                    new BehaviourComponent(),
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

        $containerConfigCache = new ConfigCache($this->compiledContainerPath, $isDebug);

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $runtime->compile($containerBuilder);
            $containerBuilder->compile();

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
        require_once $this->compiledContainerPath;

        $containerFqcn = $this->compiledContainerNamespace . '\\' . $this->compiledContainerClass;
        $container = new $containerFqcn();

        $runtime->boot($container);

        return $container;
    }
}
