# Integrator Tool
[![Build Status](https://github.com/spryker-sdk/integrator/workflows/CI/badge.svg?branch=master)](https://github.com/spryker-sdk/integrator/actions?query=workflow%3ACI+branch%3Amaster)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

Auto-migrate applications in regard to new core releases using module manifests.

## Installation

This is a development only "require-dev" library:
```
composer require --dev spryker-sdk/integrator
```

## How it works
1. Every time the integrator runs it downloads recipes from `spryker-sdk/integrator-recipes` and uses the master branch. The recipes are downloaded into this folder: `vendor/spryker-sdk/integrator/data/recipes/`.
2. It gathers the list of dependencies from `composer.json` and their installed versions from `composer.lock` of the project.
3. The integrator checks manifests (recipes) versions and collect manifests that should be applied.
4. All gathered manifests are applied to the project code and are logged in the `integrator.lock` file in the project root directory.
