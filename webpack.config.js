/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

'use strict';

const HappyPack = require('happypack');
const happyThreadPool = HappyPack.ThreadPool({ size: 10 });

module.exports = {
    context: __dirname,
    entry: './js/client',
    module: {
        // FIXME - if the entry PHP file (client.php) is edited during --watch,
        //         the bundle will be broken because the FS switch is lost/corrupted
        rules: [
            {
                test: /\.php$/,
                use: 'happypack/loader?id=phpify'
            },
            {
                test: /\.php/,
                use: 'happypack/loader?id=source-map-extraction',
                enforce: 'post'
            },
            {
                test: /\.js$/,
                exclude: /\bnode_modules\b/,
                use: {
                    loader: 'happypack/loader?id=babel'
                }
            }
        ]
    },
    output: {
        path: __dirname + '/dist/',
        filename: 'client.js'
    },
    plugins: [
        new HappyPack({
            id: 'phpify',
            threadPool: happyThreadPool,
            loaders: [
                'transform-loader?phpify'
            ]
        }),
        new HappyPack({
            id: 'source-map-extraction',
            threadPool: happyThreadPool,
            loaders: [
                'source-map-loader'
            ]
        }),
        new HappyPack({
            id: 'babel',
            threadPool: happyThreadPool,
            loaders: [
                'babel-loader?presets=env&plugins=transform-runtime'
            ]
        })
    ]
};
