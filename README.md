# Mojekucharka.net 2021

A simple web app for managing recipes

🚧 **Note:** This is work in progress.

Written in **[PHP][php]** as an SSR (server-side rendered) web app that should work without JavaScript
(but with JavaScript it is more comfortable to use, i.e. [progressive enhancement][mdn-progressive-enhancement]).

Client-side scripts are written in **[TypeScript][typescript]**
and compiled to the latest ECMAScript (JavaScript).

The app targets **only modern browsers** (as it uses the latest HTML5, CSS and ECMAScript features).


## Content

<!-- **Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)* -->
<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Description](#description)
- [Architecture](#architecture)
	- [Backend](#backend)
	- [Frontend](#frontend)
	- [Technology highlights](#technology-highlights)
		- [Features](#features)
		- [Frontend tooling](#frontend-tooling)
		- [Security](#security)
	- [Project structure](#project-structure)
- [Development](#development)
	- [Requirements](#requirements)
	- [Set up](#set-up)
	- [Available commands](#available-commands)
- [Deployment](#deployment)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->


## Description

See [this Google Docs document][mojekucharka-desc-doc] (in Czech).

[mojekucharka-desc-doc]: https://docs.google.com/document/d/1go3qb_ei5DVzYgW5VXdc5Khsog45C1alVgtiq0YLpaE/edit?usp=sharing


## Architecture

_Note: The web app was created as a semestral project in the CTU's B6B39ZWA course, so the architecture was affected
by the [semestral project's requirements][ctu-zwa-semestral-project]._


### Backend

SSR web app in PHP, work without JS

no dependencies, no frameworks, plain PHP, everything from scratch

Apache, PHP, MySQL


### Frontend

HTML5, CSS, JavaScript (TypeScript compiled to the latest ECMAScript)

no dependencies, no frameworks, plain CSS/Sass, TypeScript, everything from scratch


### Technology highlights

#### Features
* HTML5 semantic elements
* CSS – fully responsive, Flexbox, Grid, animations, transitions, transforms, media queries, ...
* [PWA web app manifest](./frontend/manifest.json)
  including [maskable icons][web-dev-maskable-icons] (but no offline support)

#### Frontend tooling
* [webpack] – an assets bundler
* [Babel][babel] – a JavaScript compiler (transpiler)
* CSS written in [Sass][sass] (SCSS), vendor prefixes automatically added by [autoprefixer]
  with [Browserslist][browserslist]

#### Security
* [Subresource Integrity (SRI)][mdn-sri]
  for all scripts and styles
* Content Security Policy (CSP) (TODO)


### Project structure

Some directories contain feature-specific READMEs. The following diagram briefly describes the main directories and
files:

```text
. (project root dir)
├── .github - GitHub config (GitHub Actions)
├── design - design files
│   └── mojekucharka-logo.afdesign - Mojekucharka.net logo (Affinity Designer file)
├── app - backend source code
│   ├── ... (TODO: update diagram once backend is finished)
│   └── index.php - application entry-point
├── config - application config
│   ├── config.local.php - app config file
│   └── config.template.php - app config template
├── frontend - frontend source code
│   ├── scripts - scripts (forms validation, AJAX, etc.)
│   ├── images - mainly the PWA app icon
│   ├── styles - styles written in Sass (SCSS)
│   ├── index.js - the frontend bundle starting point
│   ├── manifest.json - a web app manifest for PWA
│   ├── .htaccess - Apache config file
│   └── robots.txt - robots.txt
├── deploy - deployments' specific scripts, configs and code
├── build - build output (webpack, deployment, ...)
├── log - app logs
├── vendor - app dependencies and the classes autoloader (generated by running Composer)
├── node_modules - frotnend dependencies (generated by running yarn)
├── tools - custom webpack plugins
├── types - TypeScript declarations for non-code imports (PNG)
├── .browserslistrc - Browserslist config
├── .eslintrc.js - ESLint config
├── .nvmrc - Node.js version specification (may be useful for some platforms)
├── babel.config.js - Babel config
├── postcss.config.js - PostCSS config
├── package.json - npm package (frontend dev dependencies, build scripts)
├── tsconfig.json - main TypeScript config
├── webpack.config.*.js - webpack configs
└── yarn.lock – Yarn lockfile
```


## Development

TODO: document and describe motivation, usage


### Requirements

- Apache web server 2.4+ (backend)
- [PHP](https://www.php.net/manual/en/) 7.4+ (backend)
- MySQL (backend)
- [Node.js](https://nodejs.org/) 16.x (frontend tooling)
- [Yarn](https://yarnpkg.com/) 1.x (frontend tooling)
- You can follow [this Node.js Development Setup guide](./NODEJS-SETUP.md).


### Set up

TODO: document Apache, PHP, MySQL setup

**Frontend tooling:**
1. Install all dependencies with Yarn (run `yarn`).
2. You are ready to go.
3. Use `yarn start` to start dev server with HMR.
4. Then open `http://localhost:3000/` in the browser.

**Backend:**
1. Using the built-in PHP web server: `make run` or `make SERVER=addr:port run`


### Available commands

* `yarn start` – Starts a frontend development server with [HMR (hot module replacement)][webpack-hmr]. First, it builds
  the development version of the frontend assets and outputs them `dist` dir. Then it continuously and incrementally
  rebuilds the assets when sources (sourcecode) change. When it is possible (for styles and scripts), it applies the
  changes using [HMR][webpack-hmr] without the need for a full browser page refresh.

* `yarn build` – Builds the production version of the frontend assets and outputs them `dist` dir.  
  Note: Before running an actual build, `dist` folder is purged.

* `yarn analyze` – Same as `yarn build` but it also outputs `stats.json`
  and runs [webpack-bundle-analyzer CLI][webpack-bundle-analyzer-cli].

* `yarn tsc` – Runs TypeScript compiler. Outputs type errors to console.

* `yarn lint` – Runs [ESLint](https://eslint.org/). Outputs errors to console.



## Deployment

* to https://wa.toad.cz/~endlemar/:
	* see [these notes](./deploy/wa.toad.cz/README.md)
	* `make TARGET=wa.toad.cz deploy`
	* `make TARGET=wa.toad.cz destroy`


<!-- links references -->

[php]: https://www.php.net/manual/en/

[webpack]: https://webpack.js.org/

[webpack-hmr]: https://webpack.js.org/guides/hot-module-replacement/

[webpack-bundle-analyzer-cli]: https://github.com/webpack-contrib/webpack-bundle-analyzer#usage-as-a-cli-utility

[babel]: https://babeljs.io/

[sass]: https://sass-lang.com/

[autoprefixer]: https://github.com/postcss/autoprefixer

[browserslist]: https://github.com/browserslist/browserslist

[typescript]: https://www.typescriptlang.org/

[mdn-progressive-enhancement]: https://developer.mozilla.org/en-US/docs/Glossary/Progressive_Enhancement

[mdn-sri]: https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity

[web-dev-maskable-icons]: https://web.dev/maskable-icon/

[ctu-zwa-semestral-project]: https://cw.fel.cvut.cz/wiki/courses/b6b39zwa/classification/semestralka
