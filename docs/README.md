# Integrator documentation

Integrator is designed for updating modules basing on rules we define in
special manifests.
The goal is to make integrations of changes of modules to the projects
faster and reduce possible errors.

## Manifests

A manifest always contains all update instructions necessary to be
performed starting from the first (major) version.
Based on the (minor) module version it encounters it only performs the
necessary steps.

Those so-called "module manifests" reside in this repository:
https://github.com/spryker-sdk/integrator-manifests

## Manifest types

A manifest itself is a JSON file with predefined structure.
All supported types of manifests described in [manifest-types](manifest-types.md).

## Usage during development

### Installation

Install the package to  your project
```
composer require --dev spryker-sdk/integrator
```

### Running Integrator

It should be first run in dry-run mode
```
vendor/bin/integrator --dry
```
