/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

'use strict';

module.exports = {
    context: __dirname,
    entry: './js/client',
    module: {
        // FIXME - if the entry PHP file (client.php) is edited during --watch,
        //         the bundle will be broken because the FS switch is lost/corrupted
        rules: [
            {
                test: /\.php$/,
                use: ['transform-loader?phpify']
            },
            {
                test: /\.php/,
                use: ['source-map-loader'],
                enforce: 'post'
            },
            {
                test: /\.js$/,
                exclude: /\bnode_modules\b/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['env'],
                        plugins: ['transform-runtime']
                    }
                }
            }
        ]
    },
    output: {
        path: __dirname + '/dist/',
        filename: 'client.js'
    }
};
