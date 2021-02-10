Combyna
=======

[![Build Status](https://github.com/combyna/combyna/workflows/CI/badge.svg)](https://github.com/combyna/combyna/actions?query=workflow%3ACI)

Build isomorphic apps using a declarative YAML- or JSON-based language.

## Developer setup (for working on the library itself)

Clone this repo, install the PHP and JS dependencies,
build the expression language parser, build the JS bundle
and then start the PHP server.

You'll need Node.js and PHP 5.5+ to use this project.

1. `git clone https://github.com/combyna/combyna.git`
1. `cd combyna`
1. `composer install`
1. `npm install`
1. `composer run build:expression-parser`
1. `npm run dev:watch`
1. (In a separate terminal) `composer run simple-server --timeout 0`

## App config

An example app is provided under `example/simple/*`,
which is the one the `simple-server` script above will start.
The config for this app is in the `simpleApp.cyn.yml` file.
