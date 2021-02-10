<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\Delegator\EarlyDelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\CallbackPass;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterEarlyDelegateesPass;
use Combyna\Component\Common\DependencyInjection\Compiler\SequentialPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ExpressionLanguageComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionLanguageComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        // Register "early" warmers (those that are needed in order to build the service container)
        // - these will be run as part of the container compile
        $containerBuilder->addCompilerPass(new SequentialPass([
            new RegisterEarlyDelegateesPass([
                new EarlyDelegateeTagDefinition(
                    'combyna.early_cache_warmer',
                    'combyna.cache.early_warmer',
                    'addWarmer'
                )
            ]),
            new CallbackPass(function (ContainerBuilder $fullContainerBuilder) {
                $cachePath = $fullContainerBuilder->getParameter('combyna.cache_path');
                $earlyCacheWarmer = $fullContainerBuilder->get('combyna.cache.early_warmer');

                $earlyCacheWarmer->warmUp($cachePath);
            })
        ]));

        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            // Register "normal" warmers (those that are not required for building the service container)
            new DelegateeTagDefinition(
                'combyna.cache_warmer',
                'combyna.cache.warmer',
                'addWarmer'
            )
        ]));
    }
}
