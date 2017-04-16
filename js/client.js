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

const renderView = clientModule.execute().getNative()(
    {
        'libraries': [
            {
                'name': 'gui',
                'description': 'GUI tools',
                'widgets': {
                    'button': {
                        'type': 'core',
                        'attributes': {'label': 'text'},
                        'children': []
                    }
                }
            }
        ]
    },
    {
        'name': 'My test Combyna app',
        'translations': {
            'en': {
                'form': {
                    'button_label': 'Click me (translated!)'
                }
            }
        },
        'views': {
            'my_view': {
                'title': {
                    'type': 'text',
                    'text': 'My view'
                },
                'description': 'A test view, for testing',
                'widget': {
                    'type': 'gui.button',
                    'attributes': {
                        'label': {'type': 'translation', 'key': 'form.button_label'}
                    },
                    'children': null
                },
                'store': null
            }
        }
    }
);

ReactDOM.render(
    React.createElement(ViewComponent, {
        name: 'my_view',
        renderView: renderView
    }),
    document.getElementById('app')
);
