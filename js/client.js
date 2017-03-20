/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

'use strict';

var clientModule = require('../php/src/client.php')();

// Hook stdout and stderr up to the DOM
// FIXME: Move this to phpify
clientModule.getStdout().on('data', function (data) {
    document.body.insertAdjacentHTML('beforeEnd', data + '<br>');
});
clientModule.getStderr().on('data', function (data) {
    document.body.insertAdjacentHTML('beforeEnd', data + '<br>');
});

clientModule.execute();
