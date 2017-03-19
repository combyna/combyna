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
use Combyna\Component\Core\CoreComponent;
use Combyna\Component\Core\Runtime;
use Combyna\Component\Config\ConfigComponent;
use Combyna\Component\Environment\EnvironmentComponent;
use Combyna\Component\Expression\ExpressionComponent;
use Combyna\Component\ExpressionLanguage\ExpressionLanguageComponent;
use Combyna\Component\Renderer\RendererComponent;
use Combyna\Component\Type\TypeComponent;
use Combyna\Component\Ui\UiComponent;
use Combyna\Component\Validator\ValidatorComponent;
use Combyna\Container\CompiledCombynaContainer;
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
            new AppComponent(),
            new BagComponent(),
            new ConfigComponent(),
            new CoreComponent(),
            new EnvironmentComponent(),
            new ExpressionComponent(),
            new ExpressionLanguageComponent(),
            new RendererComponent(),
            new TypeComponent(),
            new UiComponent(),
            new ValidatorComponent()
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

        $runtime->boot();

        return new CompiledCombynaContainer();
    }
}
