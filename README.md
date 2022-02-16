# Integrator Tool
[![Build Status](https://github.com/spryker-sdk/integrator/workflows/CI/badge.svg?branch=master)](https://github.com/spryker-sdk/integrator/actions?query=workflow%3ACI+branch%3Amaster)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

Auto-migrate applications in regard to new core releases using recipes.

## Installation

This is a development only "require-dev" library:
```
composer require --dev spryker-sdk/integrator
```

## How it works
1. Every time when the integrator is run it uploads recipes from `spryker-sdk/integrator-recipes` and uses the master branch. They are uploaded into this folder: `vendor/spryker-sdk/integrator/data/recipes/`.
2. It gathers the list of dependencies from `composer.json` and their uploaded versions from `composer.lock` of the project.
3. The integrator checks manifests (recipes) versions and collect manifests that should be applied.
4. All gathered manifest are applied to the project code.

## How to test the specific recipe using suite-nonsplit
1. We should specify module and get content of needed manifest of this module. To do it we need change code in `\SprykerSdk\Integrator\Manifest\ManifestReader::readManifests` method:
```php
    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readManifests(array $moduleTransfers): array
    {
        return [
            'Spryker.AssetExternal' => json_decode(
                file_get_contents('vendor/spryker-sdk/integrator/data/recipes/integrator-recipes-master/AssetExternal/1.0.0/installer-manifest.json'),
                true,
            ),
        ];
    }
```
2. Open CLI:
```shell
docker/sdk cli
```
3. Run integrator:
```shell
vendor/bin/integrator
```
4. The changes from the source manifest file should be applied to target places and should appear in the `integrator.lock` file in the source project directory.

### Why should we do in this way?
The integrator gathers dependencies from the `composer.json` file and it means that no one spryker module is instaled directly. All spryker modules are installed in the `spryker/spryker` package. Because of it, we can not apply recipes to the specific spryker module.

We can test the integrator behavior and recipes as they are in the **B2C** because it installs spryker modules directly (recipes should be in the master branch of `spryker-sdk/integrator-recipes`).
