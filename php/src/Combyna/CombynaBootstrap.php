<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna;

use Combyna\Component\App\AppComponent;
use Combyna\Component\Bag\BagComponent;
use Combyna\Component\Config\ConfigComponent;
use Combyna\Component\Environment\EnvironmentComponent;
use Combyna\Component\Event\EventComponent;
use Combyna\Component\Expression\ExpressionComponent;
use Combyna\Component\ExpressionLanguage\ExpressionLanguageComponent;
use Combyna\Component\Framework\FrameworkComponent;
use Combyna\Component\Framework\Runtime;
use Combyna\Component\Instruction\InstructionComponent;
use Combyna\Component\Plugin\PluginComponent;
use Combyna\Component\Program\ProgramComponent;
use Combyna\Component\Renderer\RendererComponent;
use Combyna\Component\Router\RouterComponent;
use Combyna\Component\Signal\SignalComponent;
use Combyna\Component\Store\StoreComponent;
use Combyna\Component\Trigger\TriggerComponent;
use Combyna\Component\Type\TypeComponent;
use Combyna\Component\Ui\UiComponent;
use Combyna\Component\Validator\ValidatorComponent;
use Combyna\Container\CompiledCombynaContainer;
use Combyna\Plugin\Core\CorePlugin;
use Combyna\Plugin\Gui\GuiPlugin;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * Class CombynaBootstrap
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CombynaBootstrap
{
    /**
     * Fetches or builds the service container
     *
     * @param bool $isDebug
     * @return ContainerInterface
     */
    public function getContainer($isDebug = true)
    {
        $runtime = new Runtime([
            // Components
            new AppComponent(),
            new BagComponent(),
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
            new CorePlugin(),
            new GuiPlugin()
        ]);

        $file = __DIR__ . '/../../dist/Combyna/Container/CompiledCombynaContainer.php';

        $containerConfigCache = new ConfigCache($file, $isDebug);

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $runtime->compile($containerBuilder);
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump([
                    'namespace' => 'Combyna\Container',
                    'class' => 'CompiledCombynaContainer'
                ]),
                $containerBuilder->getResources()
            );
        }

        $container = new CompiledCombynaContainer();

        $runtime->boot($container);

        return $container;
    }
}
