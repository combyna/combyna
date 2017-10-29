/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

'use strict';

import React from 'react';
import ReactDOM from 'react-dom';
import ViewComponent from './React/Component/ViewComponent';

require('phpruntime/sync').install({
    functionGroups: [
        require('phpruntime/src/builtin/functions/pcre/basicSupport')
    ]
});
const clientModule = require('../php/src/client-entry.php')();

// Hook stdout and stderr up to the DOM
// FIXME: Move this to phpify
clientModule.getStdout().on('data', function (data) {
    if (!console) {
        return;
    }

    console.info(data);
});
clientModule.getStderr().on('data', function (data) {
    if (!console) {
        return;
    }

    console.warn(data);
});

const scriptElement = document.getElementById('appConfig');

if (!scriptElement) {
    throw new Error('Cannot find #appConfig element');
}

const fullConfigJson = scriptElement.text;
const fullConfig = JSON.parse(fullConfigJson);
const phpAPI = clientModule.execute().getNative()(
    fullConfig.environment,
    fullConfig.app
);

ReactDOM.render(
    React.createElement(ViewComponent, {
        phpAPI: phpAPI
    }),
    document.getElementById('app')
);
