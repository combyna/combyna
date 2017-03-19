<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Client;

use Combyna\Component\App\App;

/**
 * Class Client
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Client
{
    /**
     * @var App
     */
    private $app;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function start()
    {
        print 'Hello from Client.php!';
        
        $this->app->demoMe();
    }
}
